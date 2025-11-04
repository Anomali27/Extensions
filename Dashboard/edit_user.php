<?php
require_once '../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Akses ditolak! Anda bukan admin.";
    header("Location: ../auth/login.php");
    exit;
}

$id = $_POST['id'] ?? null;
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$status = $_POST['status'] ?? 'offline';

if (!$id) {
    $_SESSION['error_message'] = "Data user tidak valid!";
    header("Location: dashboard.php");
    exit;
}

if (!empty($password)) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("UPDATE users SET username=?, email=?, password=?, status=? WHERE id=?");
    $stmt->bind_param("ssssi", $username, $email, $hashed, $status, $id);
} else {
    $stmt = $connection->prepare("UPDATE users SET username=?, email=?, status=? WHERE id=?");
    $stmt->bind_param("sssi", $username, $email, $status, $id);
}

if ($stmt->execute()) {
    $_SESSION['success_message'] = "User berhasil diperbarui!";
} else {
    $_SESSION['error_message'] = "Gagal memperbarui user: " . $stmt->error;
}

$stmt->close();
header("Location: dashboard.php");
exit;
?>
