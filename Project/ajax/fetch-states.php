<?php
require '../database/db.php'; 

// Get filters
$device = $_GET['device'] ?? '';
$browser = $_GET['browser'] ?? '';
$date = $_GET['date'] ?? '';

// Base query
$sql = "SELECT * FROM browser_states WHERE 1=1";
$params = [];

// Device filter
if(!empty($device)){
    $sql .= " AND device_name = :device";
    $params[':device'] = $device;
}

// Browser filter
if(!empty($browser)){
    $sql .= " AND browser_name = :browser";
    $params[':browser'] = $browser;
}

// Date filter
if(!empty($date)){
    $sql .= " AND DATE(created_at) = :date";
    $params[':date'] = $date;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$states = $stmt->fetchAll();

if(count($states) > 0){
    foreach($states as $state){
        $badge = $state['save_type'] === 'Auto-saved' ? '<span class="badge auto">Auto-saved</span>' : '<span class="badge manual">Manual</span>';
        echo '<div class="state-card">
                <div class="state-left">
                  <div class="device-icon">';
        switch(strtolower($state['device_name'])){ //turn into icon
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
                      <span>'.$state['tab_count'].' tabs</span>
                      <span>'.$state['browser_name'].'</span>
                      '.$badge.'
                    </div>
                  </div>
                </div>
                <div class="state-actions">  
                  <a href="#" class="view-link" onclick="openModal(); return false;">View</a>
                  <button class="danger">Delete</button>
                  <button class="primary restore">Restore</button>
                </div>
              </div>';
    }
} else {
    echo '<div class="empty-state"><h3>No saved states found</h3></div>';
}
