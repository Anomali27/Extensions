<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$username = $_SESSION['user'];
// Get user id and saldo
$query = $connection->prepare("SELECT id, saldo FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$saldo = $user['saldo'];

$data = json_decode(file_get_contents('php://input'), true);

$room_id = $data['roomId'];
$start_date = $data['startDate'];
$start_time = $data['startTime']; // Single start time
$duration_hours = floatval($data['duration']); // Duration in hours
$duration_minutes = $duration_hours * 60; // Convert to minutes for DB
$package = $data['package'];
$price = $data['price'];

// Check for sufficient saldo
if ($saldo < $price) {
    echo json_encode(['success' => false, 'message' => 'Saldo tidak cukup']);
    exit;
}

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

// Begin transaction
$connection->begin_transaction();

try {
    // Deduct saldo
    $new_saldo = $saldo - $price;
    $updateSaldo = $connection->prepare("UPDATE users SET saldo = ? WHERE id = ?");
    $updateSaldo->bind_param("di", $new_saldo, $user_id);
    $updateSaldo->execute();

    // Insert booking
    $insertBooking = $connection->prepare("INSERT INTO orders (user_id, room_id, start_date, start_time, duration, package, price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
    $insertBooking->bind_param("iissdsi", $user_id, $room_id, $start_date, $start_time, $duration_minutes, $package, $price);
    $insertBooking->execute();

    $order_id = $connection->insert_id;

    // Insert payment history
    $description = "Pembayaran booking untuk room ID $room_id";
    $insertPayment = $connection->prepare("INSERT INTO payment_history (user_id, order_id, amount, description, created_at) VALUES (?, ?, ?, ?, NOW())");
    $amount = -1 * $price; // negative for debit
    $insertPayment->bind_param("iids", $user_id, $order_id, $amount, $description);
    $insertPayment->execute();

    // Update room status
    $updateRoom = $connection->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
    $updateRoom->bind_param("i", $room_id);
    $updateRoom->execute();

    // Commit transaction
    $connection->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $connection->rollback();
    echo json_encode(['success' => false, 'message' => 'Failed to process booking: ' . $e->getMessage()]);
}
?>
