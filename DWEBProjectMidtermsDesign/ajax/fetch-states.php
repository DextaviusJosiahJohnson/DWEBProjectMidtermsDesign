<?php
session_start(); // 1. Start session to get the logged-in user
require '../database/db.php'; 

// 2. Security Check: Stop if not logged in
if (!isset($_SESSION['user_id'])) {
    die('<div class="empty-state"><h3>Please log in to view states.</h3></div>');
}

$user_id = $_SESSION['user_id'];

// Get filters
$device = $_GET['device'] ?? '';
$browser = $_GET['browser'] ?? '';
$date = $_GET['date'] ?? '';

// Base query - 3. Added user_id check
$sql = "SELECT * FROM browser_states WHERE user_id = :uid";
$params = [':uid' => $user_id];

// Device filter (Corrected column name: device_name -> device)
if(!empty($device)){
    $sql .= " AND device = :device";
    $params[':device'] = $device;
}

// Browser filter (Corrected column name: browser_name -> browser)
if(!empty($browser)){
    $sql .= " AND browser = :browser";
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
        // 4. Handle missing columns cleanly
        // If 'save_type' isn't in DB yet, default to 'Manual'
        $saveType = $state['save_type'] ?? 'Manual'; 
        $badgeClass = ($saveType === 'Auto-saved') ? 'auto' : 'manual';
        
        // 5. Calculate tab count from the JSON data
        $tabs = json_decode($state['tab_data'] ?? '[]', true);
        $tabCount = is_array($tabs) ? count($tabs) : 0;

        echo '<div class="state-card">
                <div class="state-left">
                  <div class="device-icon">';
        
        // Corrected column name access
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