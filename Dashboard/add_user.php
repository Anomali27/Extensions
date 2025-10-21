<?php
require_once '../config/config.php';
session_start();

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if ($username && $email && $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password, status) VALUES ('$username', '$email', '$hashed', 'offline')";
    if (mysqli_query($connection, $sql)) {
        $_SESSION['success_message'] = "User baru berhasil ditambahkan!";
    } else {
        $_SESSION['error_message'] = "Gagal menambahkan user: " . mysqli_error($connection);
    }
} else {
    $_SESSION['error_message'] = "Form tidak lengkap!";
}

header("Location: dashboard.php");
exit;
?>
