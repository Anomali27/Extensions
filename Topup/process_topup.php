<?php
session_start();
include '../config/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// AMBIL DATA VIA POST (KARENA PAKAI FormData)
$amount = $_POST['amount'] ?? 0;
$method = $_POST['method'] ?? '';

if ($amount < 5000 || $amount % 5000 !== 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid amount']);
    exit;
}

if (!$method) {
    echo json_encode(['success' => false, 'message' => 'Invalid payment method']);
    exit;
}

// Insert ke topup_history
$stmt = $connection->prepare("
    INSERT INTO topup_history (user_id, amount, method, status) 
    VALUES (?, ?, ?, 'pending')
");
$stmt->bind_param("iis", $user_id, $amount, $method);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to insert topup record']);
    exit;
}

$topup_id = $stmt->insert_id;

// Simulasi sukses pembayaran
$status = 'success';
$update = $connection->prepare("UPDATE topup_history SET status = ? WHERE id = ?");
$update->bind_param("si", $status, $topup_id);
$update->execute();

// Update saldo user
$saldo = $connection->prepare("UPDATE users SET saldo = saldo + ? WHERE id = ?");
$saldo->bind_param("ii", $amount, $user_id);
$saldo->execute();

// Ambil saldo baru
$balance = $connection->prepare("SELECT saldo FROM users WHERE id = ?");
$balance->bind_param("i", $user_id);
$balance->execute();
$res = $balance->get_result();
$user = $res->fetch_assoc();

echo json_encode([
    'success' => true,
    'message' => 'Top up berhasil!',
    'new_balance' => $user['saldo']
]);
?>
