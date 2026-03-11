<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) exit;

$stateId = $_GET['id'];
$userId = $_SESSION['user_id'];

// 1. Verify this state belongs to the logged-in user
$stmt = $pdo->prepare("SELECT id FROM browser_states WHERE id = ? AND user_id = ?");
$stmt->execute([$stateId, $userId]);
$state = $stmt->fetch();

if ($state) {
    // 2. Fetch the tabs associated with this state
    $tabStmt = $pdo->prepare("SELECT title, url FROM tabs WHERE state_id = ? ORDER BY tab_order ASC");
    $tabStmt->execute([$stateId]);
    $tabs = $tabStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 3. Encode back to JSON for the frontend JavaScript to digest
    echo json_encode($tabs); 
}
?>