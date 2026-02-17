<?php
session_start();
require '../database/db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) exit;

$stmt = $pdo->prepare("SELECT * FROM browser_states WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$state = $stmt->fetch(PDO::FETCH_ASSOC);

if ($state) {
    // Return the JSON tab data directly
    echo $state['tab_data']; 
}
?>