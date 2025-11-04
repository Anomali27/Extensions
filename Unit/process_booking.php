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
$start_times = explode(',', $data['startTime']); // Multiple start times
$duration_hours = floatval($data['duration']); // Duration in hours
$duration_minutes = $duration_hours * 60; // Convert to minutes for DB
$package = $data['package'];
$price = $data['price'];

// Check if room is available
$query = $connection->prepare("SELECT status FROM rooms WHERE id = ?");
$query->bind_param("i", $room_id);
$query->execute();
$result = $query->get_result();
$room = $result->fetch_assoc();

if ($room['status'] !== 'available') {
    echo json_encode(['success' => false, 'message' => 'Room not available']);
    exit;
}

// Insert bookings for each selected time
$success = true;
foreach ($start_times as $start_time) {
    $start_time = trim($start_time);
    if (empty($start_time)) continue;

    // Check for conflicts with existing bookings
    $query = $connection->prepare("
        SELECT id FROM orders
        WHERE room_id = ? AND start_date = ? AND start_time = ? AND status IN ('active', 'pending')
    ");
    $query->bind_param("iss", $room_id, $start_date, $start_time);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $success = false;
        break;
    }

    // Insert booking
    $query = $connection->prepare("INSERT INTO orders (user_id, room_id, start_date, start_time, duration, package, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
    $query->bind_param("iissdsi", $user_id, $room_id, $start_date, $start_time, $duration_minutes, $package, $price);

    if (!$query->execute()) {
        $success = false;
        break;
    }
}

if ($success) {
    // Update room status to booked
    $connection->query("UPDATE rooms SET status = 'booked' WHERE id = $room_id");
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create booking or time slot already booked']);
}
?>
