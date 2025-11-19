<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    $_SESSION['error_message'] = 'Invalid room ID.';
    header('Location: dashboard.php');
    exit;
}

// Check for active bookings
$checkQuery = $connection->prepare("SELECT COUNT(*) AS count FROM orders WHERE room_id = ? AND status IN ('active', 'pending')");
$checkQuery->bind_param('i', $id);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();
$count = $checkResult->fetch_assoc()['count'];

if ($count > 0) {
    $_SESSION['error_message'] = 'Cannot delete room with active bookings.';
    header('Location: dashboard.php');
    exit;
}

// Delete room
$deleteQuery = $connection->prepare("DELETE FROM rooms WHERE id = ?");
$deleteQuery->bind_param('i', $id);
if ($deleteQuery->execute()) {
    $_SESSION['success_message'] = 'Room deleted successfully.';
} else {
    $_SESSION['error_message'] = 'Failed to delete room.';
}

header('Location: dashboard.php');
exit;
?>
