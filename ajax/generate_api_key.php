<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

// ── Auth check ────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// ── CSRF check ────────────────────────────────────────────
$token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

// ── POST only ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];

    // Generate a cryptographically secure 64-character raw key
    $rawKey = bin2hex(random_bytes(32));

    // Store only the SHA-256 hash — the raw key is never persisted
    // MIGRATION NOTE: This replaces any existing plain-text key.
    // The user must paste the returned key into their extension.
    $hashedKey = hash('sha256', $rawKey);

    $stmt = $pdo->prepare("UPDATE users SET api_key = ? WHERE id = ?");
    $stmt->execute([$hashedKey, $userId]);

    // Return the raw key once — it cannot be recovered after this response
    echo json_encode(['success' => true, 'apiKey' => $rawKey]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>