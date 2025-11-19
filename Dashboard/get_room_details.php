<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$type = $_GET['type'] ?? '';
$action = $_GET['action'] ?? '';

if (!$type || !$action) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

header('Content-Type: application/json');

switch ($action) {
    case 'units':
        // Get all units for this type
        $query = $connection->prepare("SELECT id, name, status FROM rooms WHERE type = ? ORDER BY name");
        $query->bind_param("s", $type);
        $query->execute();
        $result = $query->get_result();

        $units = [];
        while ($row = $result->fetch_assoc()) {
            $units[] = $row;
        }

        echo json_encode(['units' => $units]);
        break;

    case 'times':
        // Get available time slots for today and tomorrow
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $times = [];
        $timeSlots = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00'];

        foreach ([$today, $tomorrow] as $date) {
            foreach ($timeSlots as $time) {
                // Check if this time slot is available (no booking for this type at this time)
                $query = $connection->prepare("
                    SELECT COUNT(*) as booked
                    FROM orders o
                    JOIN rooms r ON o.room_id = r.id
                    WHERE r.type = ?
                    AND o.start_date = ?
                    AND o.start_time = ?
                    AND o.status IN ('active', 'pending')
                ");
                $query->bind_param("sss", $type, $date, $time);
                $query->execute();
                $result = $query->get_result();
                $row = $result->fetch_assoc();

                $times[] = [
                    'date' => $date,
                    'time' => $time,
                    'available' => $row['booked'] == 0
                ];
            }
        }

        echo json_encode(['times' => $times]);
        break;

    case 'bookings':
        // Get current bookings for this type
        $query = $connection->prepare("
            SELECT u.username, r.name as room_name, o.start_date, o.start_time, o.duration, o.status
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN rooms r ON o.room_id = r.id
            WHERE r.type = ?
            AND o.status IN ('active', 'pending')
            ORDER BY o.start_date, o.start_time
        ");
        $query->bind_param("s", $type);
        $query->execute();
        $result = $query->get_result();

        $bookings = [];
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }

        echo json_encode(['bookings' => $bookings]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
        break;
}
?>
