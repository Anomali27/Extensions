<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$username = $_SESSION['user'];
$query = $connection->prepare("SELECT id FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];

$data = json_decode(file_get_contents('php://input'), true);

$room_id = $data['roomId'];
$start_date = $data['startDate'];
$start_time = $data['startTime']; // Single start time
$duration_hours = floatval($data['duration']); // Duration in hours
$duration_minutes = $duration_hours * 60; // Convert to minutes for DB
$package = $data['package'];
$price = $data['price'];

// Check for conflicting bookings
$query = $connection->prepare("
    SELECT id FROM orders
    WHERE room_id = ? AND start_date = ? AND status IN ('active', 'pending')
    AND (
        (start_time < DATE_ADD(?, INTERVAL ? MINUTE) AND DATE_ADD(start_time, INTERVAL duration MINUTE) > ?)
    )
");
$query->bind_param("issis", $room_id, $start_date, $start_time, $duration_minutes, $start_time);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Room sudah dipesan pada waktu tersebut']);
    exit;
}

// Insert booking
$query = $connection->prepare("INSERT INTO orders (user_id, room_id, start_date, start_time, duration, package, price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
$query->bind_param("iissdsi", $user_id, $room_id, $start_date, $start_time, $duration_minutes, $package, $price);

if ($query->execute()) {
    // Update room status to booked
    $updateRoom = $connection->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
    $updateRoom->bind_param("i", $room_id);
    $updateRoom->execute();

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create booking']);
}
?>
