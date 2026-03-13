<?php
session_start();
require '../database/db.php';
header('Content-Type: application/json');

// ── Auth check ────────────────────────────────────────────
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode([]);
    exit;
}

// ── Input validation — cast and range-check the ID ────────
$stateId = intval($_GET['id'] ?? 0);
$userId  = $_SESSION['user_id'];

if ($stateId <= 0) {
    http_response_code(400);
    echo json_encode([]);
    exit;
}

// ── Ownership verification ────────────────────────────────
$stmt = $pdo->prepare("SELECT id FROM browser_states WHERE id = ? AND user_id = ?");
$stmt->execute([$stateId, $userId]);

if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode([]);
    exit;
}

// ── Fetch tabs ────────────────────────────────────────────
$tabStmt = $pdo->prepare(
    "SELECT title, url FROM tabs WHERE state_id = ? ORDER BY tab_order ASC"
);
$tabStmt->execute([$stateId]);
echo json_encode($tabStmt->fetchAll(PDO::FETCH_ASSOC));
?>