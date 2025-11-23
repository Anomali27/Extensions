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
$newDate = $data['newDate'] ?? null;
$newTime = $data['newTime'] ?? null;
$newDuration = $data['newDuration'] ?? null;

if (!$bookingId || !$newDate || !$newTime || !$newDuration) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Check if new time slot is available
$query = $connection->prepare("
    SELECT room_id FROM orders WHERE id = ?
");
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

// Check for conflicts with other bookings
$query = $connection->prepare("
    SELECT id FROM orders
    WHERE room_id = ? AND start_date = ? AND start_time = ? AND id != ? AND status IN ('active', 'pending')
");
$query->bind_param("issi", $roomId, $newDate, $newTime, $bookingId);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Time slot already booked']);
    exit;
}

// Update booking
$query = $connection->prepare("
    UPDATE orders SET start_date = ?, start_time = ?, duration = ? WHERE id = ?
");
$query->bind_param("ssii", $newDate, $newTime, $newDuration, $bookingId);

if ($query->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update booking']);
}
?>
