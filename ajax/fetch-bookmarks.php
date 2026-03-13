<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized', 'data' => [], 'total' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];
$search  = $_GET['search'] ?? '';
$page    = max(1, intval($_GET['page']  ?? 1));
$limit   = max(1, min(100, intval($_GET['limit'] ?? 20)));
$offset  = ($page - 1) * $limit;

// ── Build shared WHERE clause ─────────────────────────────
$where  = " WHERE user_id = :uid";
$params = [':uid' => $user_id];

if (!empty($search)) {
    $where .= " AND (title LIKE :s1 OR url LIKE :s2)";
    $params[':s1'] = '%' . $search . '%';
    $params[':s2'] = '%' . $search . '%';
}

// ── Total count ───────────────────────────────────────────
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM bookmarks" . $where);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

// ── Paginated data ────────────────────────────────────────
$dataSql = "SELECT id, title, url, created_at
            FROM bookmarks"
            . $where
            . " ORDER BY created_at DESC
               LIMIT :limit OFFSET :offset";

$dataStmt = $pdo->prepare($dataSql);
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
$dataStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$bookmarks = $dataStmt->fetchAll();

echo json_encode([
    'data'        => $bookmarks,
    'total'       => $total,
    'page'        => $page,
    'limit'       => $limit,
    'total_pages' => $total > 0 ? (int) ceil($total / $limit) : 0,
]);
?>