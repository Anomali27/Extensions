<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$bookingId = $data['bookingId'] ?? null;

if (!$bookingId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

// Get room_id before deleting the booking
$query = $connection->prepare("SELECT room_id FROM orders WHERE id = ?");
$query->bind_param("i", $bookingId);
$query->execute();
$result = $query->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
    exit;
}

$roomId = $booking['room_id'];

// Delete the booking
$query = $connection->prepare("DELETE FROM orders WHERE id = ?");
$query->bind_param("i", $bookingId);

if ($query->execute()) {
    // Reset room status to available
    $query = $connection->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
    $query->bind_param("i", $roomId);
    $query->execute();

    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to delete booking']);
}
?>
