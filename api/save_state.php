<?php
require_once __DIR__ . '/../database/db.php';
// db.php requires config.php — ALLOWED_ORIGIN is now available

// ── CORS ──────────────────────────────────────────────────
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigin = (strpos($origin, 'chrome-extension://') === 0 || $origin === ALLOWED_ORIGIN)
    ? $origin
    : ALLOWED_ORIGIN;

header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 86400");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// ── Input ─────────────────────────────────────────────────
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$apiKey      = $input['apiKey']    ?? '';
$tabs        = $input['tabs']      ?? [];
$bookmarks   = $input['bookmarks'] ?? [];
$history     = $input['history']   ?? [];
$deviceName  = $input['device']    ?? 'Extension';
$browserName = $input['browser']   ?? 'Unknown Browser';
$saveType    = $input['save_type'] ?? 'Manual';

if (empty($apiKey)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'API key is required']);
    exit;
}

// ── API key verification (SHA-256 hash comparison) ────────
$hashedKey = hash('sha256', $apiKey);

$stmt = $pdo->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->execute([$hashedKey]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid API Key']);
    exit;
}

$userId = $user['id'];

// ── Search URL parser ─────────────────────────────────────
function extractSearchData(string $url): ?array {
    $parsed = parse_url($url);
    if (!$parsed || !isset($parsed['host'], $parsed['query'])) return null;

    $host = strtolower($parsed['host']);
    parse_str($parsed['query'], $q);

    $map = [
        'google.'      => ['param' => 'q',             'engine' => 'Google'],
        'bing.com'     => ['param' => 'q',             'engine' => 'Bing'],
        'yahoo.com'    => ['param' => 'p',             'engine' => 'Yahoo'],
        'duckduckgo.com' => ['param' => 'q',           'engine' => 'DuckDuckGo'],
        'youtube.com'  => ['param' => 'search_query',  'engine' => 'YouTube'],
    ];

    foreach ($map as $domain => $config) {
        if (strpos($host, $domain) !== false && isset($q[$config['param']])) {
            $query = trim($q[$config['param']]);
            if ($query !== '') return ['engine' => $config['engine'], 'query' => $query];
        }
    }
    return null;
}

try {
    $pdo->beginTransaction();

    // ── Device ────────────────────────────────────────────
    $stmt = $pdo->prepare("SELECT id FROM devices WHERE device_name = ?");
    $stmt->execute([$deviceName]);
    $row = $stmt->fetch();
    if ($row) {
        $deviceId = $row['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO devices (device_name) VALUES (?)");
        $stmt->execute([$deviceName]);
        $deviceId = $pdo->lastInsertId();
    }

    // ── Browser ───────────────────────────────────────────
    $stmt = $pdo->prepare("SELECT id FROM browsers WHERE browser_name = ?");
    $stmt->execute([$browserName]);
    $row = $stmt->fetch();
    if ($row) {
        $browserId = $row['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO browsers (browser_name) VALUES (?)");
        $stmt->execute([$browserName]);
        $browserId = $pdo->lastInsertId();
    }

    // ── Browser state ─────────────────────────────────────
    $stateName = "Session - " . date("M d H:i");
    $stmt = $pdo->prepare(
        "INSERT INTO browser_states (user_id, state_name, device_id, browser_id, save_type)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$userId, $stateName, $deviceId, $browserId, $saveType]);
    $stateId = $pdo->lastInsertId();

    // ── Tabs ──────────────────────────────────────────────
    if (!empty($tabs)) {
        $stmt = $pdo->prepare(
            "INSERT INTO tabs (state_id, url, title, tab_order) VALUES (?, ?, ?, ?)"
        );
        foreach ($tabs as $i => $tab) {
            $stmt->execute([$stateId, $tab['url'] ?? '', $tab['title'] ?? 'Untitled', $i]);
        }
    }

    // ── Bookmarks ─────────────────────────────────────────
    if (!empty($bookmarks)) {
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO bookmarks (user_id, title, url, device_id, browser_id)
             VALUES (?, ?, ?, ?, ?)"
        );
        foreach ($bookmarks as $bm) {
            $stmt->execute([$userId, $bm['title'] ?? 'Untitled', $bm['url'] ?? '', $deviceId, $browserId]);
        }
    }

    // ── Search history ────────────────────────────────────
    if (!empty($history)) {
        $engineCache = [];
        $stmtSearch  = $pdo->prepare(
            "INSERT IGNORE INTO search_history
             (user_id, search_query, raw_url, search_engine_id, browser_id, device_id, visited_at)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($history as $item) {
            $rawUrl     = $item['url'] ?? '';
            $searchData = extractSearchData($rawUrl);
            if (!$searchData) continue;

            $engineName  = $searchData['engine'];
            $searchQuery = $searchData['query'];
            $visitedAt   = !empty($item['visited_at'])
                ? date('Y-m-d H:i:s', intval($item['visited_at']) / 1000)
                : null;

            if (!isset($engineCache[$engineName])) {
                $stmt = $pdo->prepare("SELECT id FROM search_engines WHERE engine_name = ?");
                $stmt->execute([$engineName]);
                $row = $stmt->fetch();
                if ($row) {
                    $engineCache[$engineName] = $row['id'];
                } else {
                    $stmt = $pdo->prepare("INSERT INTO search_engines (engine_name) VALUES (?)");
                    $stmt->execute([$engineName]);
                    $engineCache[$engineName] = $pdo->lastInsertId();
                }
            }

            $stmtSearch->execute([
                $userId, $searchQuery, $rawUrl,
                $engineCache[$engineName], $browserId, $deviceId, $visitedAt
            ]);
        }
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'State, bookmarks, and history saved.']);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>