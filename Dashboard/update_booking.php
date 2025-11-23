<?php
session_start();
include '../config/config.php';

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = 'Unauthorized access.';
    header('Location: dashboard.php');
    exit;
}

$bookingId = $_POST['bookingId'] ?? null;
$newDate = $_POST['date'] ?? null;
$newTime = $_POST['time'] ?? null;
$newDuration = $_POST['duration'] ?? null;

if (!$bookingId || !$newDate || !$newTime || !$newDuration) {
    $_SESSION['error_message'] = 'Missing required fields.';
    header('Location: dashboard.php');
    exit;
}

// Check if booking exists and get room_id
$query = $connection->prepare("
    SELECT room_id FROM orders WHERE id = ?
");
$query->bind_param("i", $bookingId);
$query->execute();
$result = $query->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    $_SESSION['error_message'] = 'Booking not found.';
    header('Location: dashboard.php');
    exit;
}

$roomId = $booking['room_id'];

// Check time slot conflict for this room
$query = $connection->prepare("
    SELECT id FROM orders
    WHERE room_id = ? AND start_date = ? AND start_time = ? AND id != ? AND status IN ('active', 'pending')
");
$query->bind_param("issi", $roomId, $newDate, $newTime, $bookingId);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error_message'] = 'Time slot already booked.';
    header('Location: dashboard.php');
    exit;
}

// Update booking
$query = $connection->prepare("
    UPDATE orders SET start_date = ?, start_time = ?, duration = ? WHERE id = ?
");
$query->bind_param("ssii", $newDate, $newTime, $newDuration, $bookingId);

if ($query->execute()) {
    $_SESSION['success_message'] = 'Booking updated successfully.';
    header('Location: dashboard.php');
    exit;
} else {
    $_SESSION['error_message'] = 'Failed to update booking.';
    header('Location: dashboard.php');
    exit;
}
?>
