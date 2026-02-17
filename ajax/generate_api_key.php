<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];

    // 2. Generate a secure 64-character Hex Key
    // random_bytes(32) gives 32 bytes, bin2hex converts to 64 chars
    $newKey = bin2hex(random_bytes(32));

    // 3. Update Database
    $stmt = $pdo->prepare("UPDATE users SET api_key = ? WHERE id = ?");
    $stmt->execute([$newKey, $userId]);

    // 4. Return the new key
    echo json_encode(['success' => true, 'apiKey' => $newKey]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>