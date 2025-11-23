<?php
ini_set('display_errors', 0);
error_reporting(0);

session_start();
include '../config/config.php';

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = 'Unauthorized access.';
    header('Location: dashboard.php');
    exit;
}

$bookingId = $_POST['bookingId'] ?? null;

if (!$bookingId) {
    $_SESSION['error_message'] = 'Missing booking ID.';
    header('Location: dashboard.php');
    exit;
}

// Get room_id before deleting the booking
$query = $connection->prepare("SELECT room_id FROM orders WHERE id = ?");
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

// Delete the booking
$query = $connection->prepare("DELETE FROM orders WHERE id = ?");
if (!$query) {
    $_SESSION['error_message'] = 'Prepare failed: ' . $connection->error;
    header('Location: dashboard.php');
    exit;
}
$query->bind_param("i", $bookingId);

if ($query->execute()) {
    // Reset room status to available
    $query = $connection->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
    if (!$query) {
        $_SESSION['error_message'] = 'Prepare failed: ' . $connection->error;
        header('Location: dashboard.php');
        exit;
    }
    $query->bind_param("i", $roomId);
    $query->execute();

    $_SESSION['success_message'] = 'Booking deleted successfully.';
    header('Location: dashboard.php');
    exit;
} else {
    $_SESSION['error_message'] = 'Failed to delete booking: ' . $query->error;
    header('Location: dashboard.php');
    exit;
}
?>
