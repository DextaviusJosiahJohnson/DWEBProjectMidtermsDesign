<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id'])) {
    die('<div class="empty-state"><h3>Please log in to view bookmarks.</h3></div>');
}

$user_id = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM bookmarks WHERE user_id = :uid";
$params = [':uid' => $user_id];

if(!empty($search)){
    $sql .= " AND (title LIKE :search_title OR url LIKE :search_url)";
    $params[':search_title'] = "%" . $search . "%";
    $params[':search_url']   = "%" . $search . "%";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookmarks = $stmt->fetchAll();

if(count($bookmarks) > 0){

    foreach($bookmarks as $bookmark){

        echo '
        <div class="state-card">
            <div class="state-left">
                <div class="device-icon">🔖</div>
                <div class="state-info">
                    <h4>'.htmlspecialchars($bookmark['title']).'</h4>
                    <div class="meta">
                        <span>'.htmlspecialchars($bookmark['url']).'</span>
                    </div>
                    <div class="meta">
                        <span>'.date("M d, Y", strtotime($bookmark['created_at'])).'</span>
                    </div>
                </div>
            </div>
            <div class="state-actions">
                <a href="'.htmlspecialchars($bookmark['url']).'" target="_blank" class="view-link">View</a>
                <button class="danger" data-id="'.$bookmark['id'].'">Delete</button>
            </div>
        </div>';
    }

} else {
    echo '<div class="empty-state"><h3>No bookmarks found</h3></div>';
}
?>