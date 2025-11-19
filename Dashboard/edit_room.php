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
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';

if ($id <= 0 || empty($name) || empty($type)) {
    $_SESSION['error_message'] = 'Invalid input data.';
    header('Location: dashboard.php');
    exit;
}

// Update room
$updateQuery = $connection->prepare("UPDATE rooms SET name = ?, type = ? WHERE id = ?");
$updateQuery->bind_param('ssi', $name, $type, $id);
if ($updateQuery->execute()) {
    $_SESSION['success_message'] = 'Room updated successfully.';
} else {
    $_SESSION['error_message'] = 'Failed to update room.';
}

header('Location: dashboard.php');
exit;
?>
