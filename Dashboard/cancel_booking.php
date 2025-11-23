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

// Update booking status to cancelled
$query = $connection->prepare("
    UPDATE orders SET status = 'cancelled' WHERE id = ?
");
if (!$query) {
    $_SESSION['error_message'] = 'Prepare failed: ' . $connection->error;
    header('Location: dashboard.php');
    exit;
}
$query->bind_param("i", $bookingId);

if ($query->execute()) {
    $_SESSION['success_message'] = 'Booking cancelled successfully.';
    $query->close();
    header('Location: dashboard.php');
    exit;
} else {
    $_SESSION['error_message'] = 'Failed to cancel booking: ' . $query->error;
    $query->close();
    header('Location: dashboard.php');
    exit;
}
?>
