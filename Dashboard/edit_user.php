<?php
require_once '../config/config.php';
session_start();

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
    $sql = "UPDATE users 
            SET username='$username', email='$email', password='$hashed', status='$status' 
            WHERE id=$id";
} else {
    $sql = "UPDATE users 
            SET username='$username', email='$email', status='$status' 
            WHERE id=$id";
}

if (mysqli_query($connection, $sql)) {
    $_SESSION['success_message'] = "User berhasil diperbarui!";
} else {
    $_SESSION['error_message'] = "Gagal memperbarui user: " . mysqli_error($connection);
}

header("Location: dashboard.php");
exit;
?>
