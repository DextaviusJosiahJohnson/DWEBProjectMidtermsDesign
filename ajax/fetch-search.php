<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    die('<div class="empty-state"><h3>Please log in to view search history.</h3></div>');
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// 1. Use JOINs to connect the new 3NF tables
$sql = "SELECT sh.*, se.engine_name AS search_engine, b.browser_name AS browser 
        FROM search_history sh
        LEFT JOIN search_engines se ON sh.search_engine_id = se.id
        LEFT JOIN browsers b ON sh.browser_id = b.id
        WHERE sh.user_id = :uid";
$params = [':uid' => $user_id];

// 2. Filter using the correct table alias
if(!empty($search)){
    $sql .= " AND sh.search_query LIKE :search";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY sh.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$history = $stmt->fetchAll();

if(count($history) > 0){
    foreach($history as $item){
        echo '
        <div class="state-card">
            <div class="state-left">
                <div class="device-icon">🔍</div>
                <div class="state-info">
                    <h4>'.htmlspecialchars($item['search_query']).'</h4>
                    <div class="meta">
                        <span>'.htmlspecialchars($item['search_engine'] ?? 'Unknown Engine').'</span>
                        <span>'.htmlspecialchars($item['browser'] ?? 'Unknown Browser').'</span>
                    </div>
                    <div class="meta">
                        <span>'.date("M d, Y • h:i A", strtotime($item['created_at'])).'</span>
                    </div>
                </div>
            </div>
            <div class="state-actions">
                <button class="danger" data-id="'.$item['id'].'">Delete</button>
            </div>
        </div>';
    }
} else {
    echo '<div class="empty-state"><h3>No search history found</h3></div>';
}
?>