<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'];

if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

// Update booking status to cancelled
$query = $connection->prepare("
    UPDATE orders SET status = 'cancelled' WHERE id = ?
");
$query->bind_param("i", $bookingId);

if ($query->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
}
?>
