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
$where  = " WHERE sh.user_id = :uid";
$params = [':uid' => $user_id];

if (!empty($search)) {
    $where .= " AND sh.search_query LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

// ── Total count ───────────────────────────────────────────
$countSql = "SELECT COUNT(*)
             FROM search_history sh
             LEFT JOIN search_engines se ON sh.search_engine_id = se.id
             LEFT JOIN browsers b        ON sh.browser_id       = b.id"
             . $where;

$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();

// ── Paginated data ────────────────────────────────────────
$dataSql = "SELECT sh.id, sh.search_query, sh.created_at,
            se.engine_name AS search_engine,
            b.browser_name AS browser
            FROM search_history sh
            LEFT JOIN search_engines se ON sh.search_engine_id = se.id
            LEFT JOIN browsers b        ON sh.browser_id       = b.id"
            . $where
            . " ORDER BY sh.created_at DESC
               LIMIT :limit OFFSET :offset";

$dataStmt = $pdo->prepare($dataSql);
foreach ($params as $key => $val) {
    $dataStmt->bindValue($key, $val);
}
$dataStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$dataStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$dataStmt->execute();
$history = $dataStmt->fetchAll();

echo json_encode([
    'data'        => $history,
    'total'       => $total,
    'page'        => $page,
    'limit'       => $limit,
    'total_pages' => $total > 0 ? (int) ceil($total / $limit) : 0,
]);
?>