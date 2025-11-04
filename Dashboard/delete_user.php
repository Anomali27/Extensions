<?php
require_once '../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Akses ditolak! Anda bukan admin.";
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error_message'] = "ID user tidak ditemukan!";
    header("Location: dashboard.php");
    exit;
}

$stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User berhasil dihapus!";
} else {
    $_SESSION['error_message'] = "Gagal menghapus user: " . $stmt->error;
}

$stmt->close();
header("Location: dashboard.php");
exit;
?>
