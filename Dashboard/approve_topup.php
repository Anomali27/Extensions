<?php
require_once '../config/config.php';
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];
$amount = $data['amount'];
$userId = $data['userId'];

if (!$id || !$amount || !$userId) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

// Update status to success
$update_stmt = $connection->prepare("UPDATE topup_history SET status = 'success' WHERE id = ?");
$update_stmt->bind_param("i", $id);
if (!$update_stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    exit;
}

// Update user saldo
$saldo_stmt = $connection->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
$saldo_stmt->bind_param("ii", $amount, $userId);
if (!$saldo_stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update balance']);
    exit;
}

echo json_encode(['success' => true]);
?>
