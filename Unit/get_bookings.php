<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

$room_id = $_GET['room_id'] ?? '';
$date = $_GET['date'] ?? '';

if (!$room_id || !$date) {
    echo json_encode([]);
    exit;
}

$query = $connection->prepare("
    SELECT start_time, duration
    FROM orders
    WHERE room_id = ? AND start_date = ? AND status IN ('active', 'pending')
");
$query->bind_param("is", $room_id, $date);
$query->execute();
$result = $query->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

echo json_encode($bookings);
?>
