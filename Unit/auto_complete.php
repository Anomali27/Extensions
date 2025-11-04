<?php
include '../config/config.php';

// Get all active orders
$query = $connection->query("SELECT * FROM orders WHERE status = 'active'");
while ($booking = $query->fetch_assoc()) {
    $start_datetime = strtotime($booking['start_date'] . ' ' . $booking['start_time']);
    $end_datetime = $start_datetime + ($booking['duration'] * 60); // duration in minutes

    if (time() >= $end_datetime) {
        // Complete the order
        $connection->query("UPDATE orders SET status = 'completed' WHERE id = " . $booking['id']);
        // Free up the room
        $connection->query("UPDATE rooms SET status = 'available' WHERE id = " . $booking['room_id']);
    }
}

echo "Auto completion check completed.";
?>
