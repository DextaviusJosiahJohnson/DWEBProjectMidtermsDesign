<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'data' => [], 'total' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$device  = $_GET['device']  ?? '';
$browser = $_GET['browser'] ?? '';
$date    = $_GET['date']    ?? '';
$page    = max(1, intval($_GET['page']  ?? 1));
$limit   = max(1, min(100, intval($_GET['limit'] ?? 20)));
$offset  = ($page - 1) * $limit;

// ── Build shared WHERE clause ─────────────────────────────
$where  = " WHERE bs.user_id = :uid";
$params = [':uid' => $user_id];

if (!empty($device))  { $where .= " AND d.device_name  = :device";  $params[':device']  = $device;  }
if (!empty($browser)) { $where .= " AND b.browser_name = :browser"; $params[':browser'] = $browser; }
if (!empty($date))    { $where .= " AND DATE(bs.created_at) = :date"; $params[':date']  = $date;    }

// ── Total count ───────────────────────────────────────────
$countSql = "SELECT COUNT(*)
             FROM browser_states bs
             LEFT JOIN devices  d ON bs.device_id  = d.id
             LEFT JOIN browsers b ON bs.browser_id = b.id"
             . $where;

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

// ── Paginated data ────────────────────────────────────────
$dataSql = "SELECT bs.id, bs.created_at, bs.save_type,
            d.device_name  AS device,
            b.browser_name AS browser,
            (SELECT COUNT(*) FROM tabs t WHERE t.state_id = bs.id) AS tab_count
            FROM browser_states bs
            LEFT JOIN devices  d ON bs.device_id  = d.id
            LEFT JOIN browsers b ON bs.browser_id = b.id"
            . $where
            . " ORDER BY bs.created_at DESC
               LIMIT :limit OFFSET :offset";

$dataStmt = $pdo->prepare($dataSql);
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
$dataStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$states = $dataStmt->fetchAll();

echo json_encode([
    'data'        => $states,
    'total'       => $total,
    'page'        => $page,
    'limit'       => $limit,
    'total_pages' => $total > 0 ? (int) ceil($total / $limit) : 0,
]);
?>