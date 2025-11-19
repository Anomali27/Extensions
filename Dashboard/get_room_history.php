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

// Query booking history for the room
$historyQuery = $connection->prepare("
    SELECT o.id, u.username, o.start_date, o.start_time, o.duration, o.status
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.room_id = ?
    ORDER BY o.start_date DESC, o.start_time DESC
    LIMIT 50
");
$historyQuery->bind_param('i', $roomId);
$historyQuery->execute();
$historyResult = $historyQuery->get_result();
$bookings = [];
while ($row = $historyResult->fetch_assoc()) {
    $bookings[] = $row;
}

// Return JSON
header('Content-Type: application/json');
echo json_encode([
    'bookings' => $bookings
]);
?>
