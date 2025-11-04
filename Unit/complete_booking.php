<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['bookingId'];

// Update order status to completed
$query = $connection->prepare("UPDATE orders SET status = 'completed' WHERE id = ?");
$query->bind_param("i", $booking_id);

if ($query->execute()) {
    // Update room status to available
    $query = $connection->prepare("SELECT room_id FROM orders WHERE id = ?");
    $query->bind_param("i", $booking_id);
    $query->execute();
    $result = $query->get_result();
    $booking = $result->fetch_assoc();
    $room_id = $booking['room_id'];

    $connection->query("UPDATE rooms SET status = 'available' WHERE id = $room_id");

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to complete booking']);
}
?>
