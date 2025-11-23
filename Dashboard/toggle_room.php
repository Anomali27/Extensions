<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

// Check if admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$roomId = $data['roomId'] ?? null;
$newStatus = $data['newStatus'] ?? null;

if (!$roomId || !$newStatus) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate status
if (!in_array($newStatus, ['available', 'booked'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update room status
$query = $connection->prepare("
    UPDATE rooms SET status = ? WHERE id = ?
");
$query->bind_param("si", $newStatus, $roomId);

if ($query->execute()) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update room status']);
}
?>
c