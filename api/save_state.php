<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Headers: Content-Type");

// Adjust path if necessary depending on where this file is located
require '../database/db.php';

// 1. Receive JSON Data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'error' => 'No data received']);
    exit;
}

$apiKey = $input['apiKey'] ?? '';
$tabs   = $input['tabs'] ?? [];
$device = $input['device'] ?? 'Extension';
// Fix: Use the browser provided by input, or default to Unknown
$browser = $input['browser'] ?? 'Unknown Browser'; 

// 2. Verify User
$stmt = $pdo->prepare("SELECT id FROM users WHERE api_key = ?");
$stmt->execute([$apiKey]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid API Key']);
    exit;
}

// 3. Save to Database
try {
    $stmt = $pdo->prepare("INSERT INTO browser_states (user_id, state_name, device, browser, tab_data, save_type) VALUES (?, ?, ?, ?, ?, ?)");
    
    $stateName = "Session - " . date("M d H:i");
    $jsonTabs = json_encode($tabs);
    
    $stmt->execute([$user['id'], $stateName, $device, $browser, $jsonTabs, 'Manual']);

    echo json_encode(['success' => true, 'message' => 'State saved successfully!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>