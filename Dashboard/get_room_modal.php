<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;
if ($roomId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid room ID']);
    exit;
}

// Query room details
$roomQuery = $connection->prepare("SELECT id, name, type, status FROM rooms WHERE id = ?");
$roomQuery->bind_param('i', $roomId);
$roomQuery->execute();
$roomResult = $roomQuery->get_result();
if ($roomResult->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'Room not found']);
    exit;
}
$room = $roomResult->fetch_assoc();

// Query today's bookings
$today = date('Y-m-d');
$bookingQuery = $connection->prepare("
    SELECT o.id, u.username, o.start_time, o.duration, o.status
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.room_id = ? AND o.start_date = ? AND o.status IN ('active', 'pending')
    ORDER BY o.start_time
");
$bookingQuery->bind_param('is', $roomId, $today);
$bookingQuery->execute();
$bookingResult = $bookingQuery->get_result();
$bookings = [];
while ($row = $bookingResult->fetch_assoc()) {
    $bookings[] = $row;
}

// Calculate available slots: 1-hour slots from 09:00 to 21:00
$slots = [];
$startHour = 9;
$endHour = 21;
for ($hour = $startHour; $hour < $endHour; $hour++) {
    $slotStart = sprintf('%02d:00', $hour);
    $slotEnd = sprintf('%02d:00', $hour + 1);
    $available = true;

    // Check if slot overlaps with any booking
    foreach ($bookings as $booking) {
        $bookingStart = strtotime($booking['start_time']);
        $bookingEnd = strtotime($booking['start_time']) + ($booking['duration'] * 60); // duration in minutes
        $slotStartTime = strtotime($slotStart);
        $slotEndTime = strtotime($slotEnd);

        if ($slotStartTime < $bookingEnd && $slotEndTime > $bookingStart) {
            $available = false;
            break;
        }
    }

    if ($available) {
        $slots[] = $slotStart . ' - ' . $slotEnd;
    }
}

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'room' => $room,
    'bookings' => $bookings,
    'available_slots' => $slots
]);
?>
