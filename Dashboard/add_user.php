<?php
require_once '../config/config.php';
session_start();
ob_start(); // Start output buffering to prevent unwanted output

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        echo json_encode(['success' => false, 'message' => 'Akses ditolak! Anda bukan admin.']);
        exit;
    }
    $_SESSION['error_message'] = "Akses ditolak! Anda bukan admin.";
    header("Location: ../auth/login.php");
    exit;
}

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$response = ['success' => false, 'message' => 'Form tidak lengkap!'];

if ($username && $email && $password) {
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $connection->prepare("INSERT INTO users (username, email, password, status) VALUES (?, ?, ?, 'offline')");
    $stmt->bind_param("sss", $username, $email, $hashed);

    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'User baru berhasil ditambahkan!'];
    } else {
        $response = ['success' => false, 'message' => "Gagal menambahkan user: " . $stmt->error];
    }
    $stmt->close();
}

ob_end_clean(); // Clean output buffer before sending json

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    if ($response['success']) {
        $_SESSION['success_message'] = $response['message'];
    } else {
        $_SESSION['error_message'] = $response['message'];
    }
    header("Location: /Dashboard/dashboard.php");
    exit;
}
?>
