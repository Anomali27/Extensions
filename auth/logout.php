<?php
require_once '../config/config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $update = $connection->prepare("UPDATE users SET status = 'offline' WHERE id = ?");
    $update->bind_param("i", $_SESSION['user_id']);
    $update->execute();
}

session_destroy();
header("Location: ../auth/auth.php");
exit;
?>
