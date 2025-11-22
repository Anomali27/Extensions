<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

$bookingId = $_GET['bookingId'] ?? null;
if (!$bookingId) {
    echo json_encode(['success' => false, 'message' => 'bookingId parameter is required']);
    exit;
}

$query = $connection->prepare("SELECT started_at FROM orders WHERE id = ?");
$query->bind_param("i", $bookingId);
$query->execute();
$result = $query->get_result();
$row = $result->fetch_assoc();

if ($row) {
    echo json_encode(['success' => true, 'started_at' => $row['started_at']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found']);
}
?>
