<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    die('<div class="empty-state"><h3>Please log in to view search history.</h3></div>');
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM search_history WHERE user_id = :uid";
$params = [':uid' => $user_id];

if(!empty($search)){
    $sql .= " AND search_query LIKE :search";
    $params[':search'] = "%$search%";
}

$sql .= " ORDER BY created_at DESC";

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
                        <span>'.htmlspecialchars($item['search_engine']).'</span>
                        <span>'.htmlspecialchars($item['browser']).'</span>
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