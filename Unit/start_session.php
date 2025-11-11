<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$bookingId = $data['bookingId'];

if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'Booking ID required']);
    exit;
}

// Update booking with started_at timestamp
$query = $connection->prepare("UPDATE orders SET started_at = NOW() WHERE id = ? AND status = 'active'");
$query->bind_param("i", $bookingId);

if ($query->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to start session']);
}
?>
