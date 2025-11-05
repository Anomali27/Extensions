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

// Always succeed - insert booking regardless
$query = $connection->prepare("INSERT INTO orders (user_id, room_id, start_date, start_time, duration, package, price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
$query->bind_param("iissdsi", $user_id, $room_id, $start_date, $start_time, $duration_minutes, $package, $price);

$query->execute();

echo json_encode(['success' => true]);
?>
