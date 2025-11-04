<?php
require_once '../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Akses ditolak"]);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(["error" => "ID tidak ditemukan"]);
    exit;
}

$stmt = $connection->prepare("SELECT id, username, email, status FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($data = $result->fetch_assoc()) {
    echo json_encode($data);
} else {
    echo json_encode(["error" => "User tidak ditemukan"]);
}

$stmt->close();
?>
