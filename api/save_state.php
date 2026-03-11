<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Content-Type");

require '../database/db.php';

// 1. Receive JSON Data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$apiKey = $input['apiKey'] ?? '';
$tabs   = $input['tabs'] ?? [];
$bookmarks = $input['bookmarks'] ?? []; 
$history = $input['history'] ?? [];     

$deviceName = $input['device'] ?? 'Extension';
$browserName = $input['browser'] ?? 'Unknown Browser'; 
$saveType = $input['save_type'] ?? 'Manual';

// 2. Verify User
$stmt = $pdo->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->execute([$apiKey]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid API Key']);
    exit;
}

$userId = $user['id'];

// Helper Function: Parse URLs into Search Queries
function extractSearchData($url) {
    $parsed = parse_url($url);
    if (!$parsed || !isset($parsed['host']) || !isset($parsed['query'])) return null;

    $host = strtolower($parsed['host']);
    parse_str($parsed['query'], $queryParams);

    $engine = null;
    $query = null;

    if (strpos($host, 'google.') !== false && isset($queryParams['q'])) {
        $engine = 'Google'; $query = $queryParams['q'];
    } elseif (strpos($host, 'bing.com') !== false && isset($queryParams['q'])) {
        $engine = 'Bing'; $query = $queryParams['q'];
    } elseif (strpos($host, 'yahoo.com') !== false && isset($queryParams['p'])) {
        $engine = 'Yahoo'; $query = $queryParams['p'];
    } elseif (strpos($host, 'duckduckgo.com') !== false && isset($queryParams['q'])) {
        $engine = 'DuckDuckGo'; $query = $queryParams['q'];
    } elseif (strpos($host, 'youtube.com') !== false && isset($queryParams['search_query'])) {
        $engine = 'YouTube'; $query = $queryParams['search_query'];
    }

    if ($engine && $query && trim($query) !== '') {
        return ['engine' => $engine, 'query' => trim($query)];
    }
    return null;
}

try {
    // Start Transaction for 3NF Relational Integrity
    $pdo->beginTransaction();

    // 3. Lookup or Insert Device
    $stmt = $pdo->prepare("SELECT id FROM devices WHERE device_name = ?");
    $stmt->execute([$deviceName]);
    $deviceRow = $stmt->fetch();
    if ($deviceRow) {
        $deviceId = $deviceRow['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO devices (device_name) VALUES (?)");
        $stmt->execute([$deviceName]);
        $deviceId = $pdo->lastInsertId();
    }

    // 4. Lookup or Insert Browser
    $stmt = $pdo->prepare("SELECT id FROM browsers WHERE browser_name = ?");
    $stmt->execute([$browserName]);
    $browserRow = $stmt->fetch();
    if ($browserRow) {
        $browserId = $browserRow['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO browsers (browser_name) VALUES (?)");
        $stmt->execute([$browserName]);
        $browserId = $pdo->lastInsertId();
    }

    // 5. Save the Session State
    $stmt = $pdo->prepare("INSERT INTO browser_states (user_id, state_name, device_id, browser_id, save_type) VALUES (?, ?, ?, ?, ?)");
    $stateName = "Session - " . date("M d H:i");
    $stmt->execute([$userId, $stateName, $deviceId, $browserId, $saveType]);
    $stateId = $pdo->lastInsertId();

    // 6. Save the Tabs
    if (!empty($tabs)) {
        $stmt = $pdo->prepare("INSERT INTO tabs (state_id, url, title, tab_order) VALUES (?, ?, ?, ?)");
        foreach ($tabs as $index => $tab) {
            $url = $tab['url'] ?? '';
            $title = $tab['title'] ?? 'Untitled';
            $stmt->execute([$stateId, $url, $title, $index]);
        }
    }

    // 7. Save Bookmarks (Using INSERT IGNORE to prevent duplicates)
    if (!empty($bookmarks)) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO bookmarks (user_id, title, url, device_id, browser_id) VALUES (?, ?, ?, ?, ?)");
        foreach ($bookmarks as $bm) {
            $url = $bm['url'] ?? '';
            $title = $bm['title'] ?? 'Untitled';
            $stmt->execute([$userId, $title, $url, $deviceId, $browserId]);
        }
    }

// 8. Save Search History
    if (!empty($history)) {
        $engineCache = []; 
        
        // ADDED "IGNORE" BACK: This tells MySQL to quietly skip duplicates instead of crashing
        $stmtSearch = $pdo->prepare("INSERT IGNORE INTO search_history (user_id, search_query, raw_url, search_engine_id, browser_id, device_id) VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($history as $histItem) {
            $rawUrl = $histItem['url'] ?? '';
            $searchData = extractSearchData($rawUrl);
            
            if ($searchData) {
                $engineName = $searchData['engine'];
                $searchQuery = $searchData['query'];
                
                // Lookup or Insert Search Engine
                if (!isset($engineCache[$engineName])) {
                    $stmt = $pdo->prepare("SELECT id FROM search_engines WHERE engine_name = ?");
                    $stmt->execute([$engineName]);
                    $engineRow = $stmt->fetch();
                    
                    if ($engineRow) {
                        $engineCache[$engineName] = $engineRow['id'];
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO search_engines (engine_name) VALUES (?)");
                        $stmt->execute([$engineName]);
                        $engineCache[$engineName] = $pdo->lastInsertId();
                    }
                }
                
                $engineId = $engineCache[$engineName];
                
                // Execute the insert (duplicates will be silently ignored by the DB)
                $stmtSearch->execute([$userId, $searchQuery, $rawUrl, $engineId, $browserId, $deviceId]);
            }
        }
    }

    // 9. Commit the Transaction
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'State, Bookmarks, and History saved successfully!']);

} catch (PDOException $e) {
    // Rollback changes if anything fails
    $pdo->rollBack();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>