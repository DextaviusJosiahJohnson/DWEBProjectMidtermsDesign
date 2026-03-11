<?php
session_start();
require '../database/db.php'; 

if (!isset($_SESSION['user_id'])) {
    die('<div class="empty-state"><h3>Please log in to view states.</h3></div>');
}

$user_id = $_SESSION['user_id'];
$device = $_GET['device'] ?? '';
$browser = $_GET['browser'] ?? '';
$date = $_GET['date'] ?? '';

// 1. Base query with JOINs and a subquery for tab count
$sql = "SELECT bs.*, d.device_name AS device, b.browser_name AS browser,
        (SELECT COUNT(*) FROM tabs t WHERE t.state_id = bs.id) AS tab_count
        FROM browser_states bs
        LEFT JOIN devices d ON bs.device_id = d.id
        LEFT JOIN browsers b ON bs.browser_id = b.id
        WHERE bs.user_id = :uid";
$params = [':uid' => $user_id];

// 2. Filters (Now filtering against the joined table columns)
if(!empty($device)){
    $sql .= " AND d.device_name = :device";
    $params[':device'] = $device;
}
if(!empty($browser)){
    $sql .= " AND b.browser_name = :browser";
    $params[':browser'] = $browser;
}
if(!empty($date)){
    $sql .= " AND DATE(bs.created_at) = :date";
    $params[':date'] = $date;
}

$sql .= " ORDER BY bs.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$states = $stmt->fetchAll();

if(count($states) > 0){
    foreach($states as $state){
        $saveType = $state['save_type'] ?? 'Manual'; 
        $badgeClass = ($saveType === 'Auto-saved') ? 'auto' : 'manual';
        $tabCount = $state['tab_count'];

        echo '<div class="state-card">
                <div class="state-left">
                  <div class="device-icon">';
        
        $deviceVal = $state['device'] ?? 'Laptop';
        switch(strtolower($deviceVal)){ 
            case 'laptop': echo '💻'; break;
            case 'desktop': echo '🖥️'; break;
            case 'work pc': echo '🖥️'; break;
            case 'mobile': echo '📱'; break;
            default: echo '💻';
        }
        echo '</div>
                  <div class="state-info">
                    <h4>'.date("M d, Y", strtotime($state['created_at'])).'</h4>
                    <div class="meta">
                      <span>'.$tabCount.' tabs</span>
                      <span>'.htmlspecialchars($state['browser'] ?? 'Unknown').'</span>
                      <span class="badge '.$badgeClass.'">'.$saveType.'</span>
                    </div>
                  </div>
                </div>
                <div class="state-actions">  
                  <a href="#" class="view-link" onclick="openModal('.$state['id'].'); return false;">View</a>
                  <button class="danger">Delete</button>
                  <button class="primary restore">Restore</button>
                </div>
              </div>';
    }
} else {
    echo '<div class="empty-state"><h3>No saved states found</h3></div>';
}
?>