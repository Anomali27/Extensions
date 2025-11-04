<?php
require_once '../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Akses ditolak! Anda bukan admin.";
    header("Location: ../auth/login.php");
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($username && $email && $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO users (username, email, password, status) VALUES (?, ?, ?, 'offline')");
    $stmt->bind_param("sss", $username, $email, $hashed);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "User baru berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan user: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Form tidak lengkap!";
}

header("Location: dashboard.php");
exit;
?>
