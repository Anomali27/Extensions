<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $quantity = (int)$_POST['quantity_available'];

    if ($quantity < 0) {
        $_SESSION['error_message'] = 'Quantity cannot be negative.';
        header('Location: dashboard.php');
        exit;
    }

    $stmt = $connection->prepare("UPDATE inventory SET quantity_available = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = 'Inventory quantity updated successfully.';
    } else {
        $_SESSION['error_message'] = 'Failed to update inventory quantity.';
    }

    $stmt->close();
    header('Location: dashboard.php');
    exit;
}
?>
