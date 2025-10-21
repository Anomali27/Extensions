<?php
require_once '../config/config.php';
session_start();

$id = $_GET['id'] ?? null;
if (!$id) {
    $_SESSION['error_message'] = "ID user tidak ditemukan!";
    header("Location: dashboard.php");
    exit;
}

$query = "DELETE FROM users WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User berhasil dihapus!";
} else {
    $_SESSION['error_message'] = "Gagal menghapus user!";
}

header("Location: dashboard.php");
exit;
?>
