<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

try {
    // --- DELETE ACCOUNT ---
    if ($action === 'delete_account') {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        session_destroy();
        echo json_encode(['success' => true]);
        exit;
    }

    // --- CHANGE PASSWORD ---
    if ($action === 'change_password') {
        $current = $input['currentPassword'];
        $new = $input['newPassword'];

        // 1. Verify old password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current, $user['password'])) {
            echo json_encode(['success' => false, 'error' => 'Current password is incorrect']);
            exit;
        }

        // 2. Update to new password
        $newHash = password_hash($new, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$newHash, $user_id]);

        echo json_encode(['success' => true]);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>