<?php
// Tentukan header untuk memastikan respons adalah JSON
header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

session_start();
include '../config/config.php';

// Inisialisasi array respons
$response = ['status' => 'error', 'message' => 'Terjadi kesalahan tidak diketahui.'];

// 1. Validasi Akses dan Input
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $response['message'] = 'Akses tidak sah.';
    echo json_encode($response);
    exit;
}

$bookingId = $_POST['id'] ?? null; // Ganti 'bookingId' menjadi 'id' agar sesuai dengan data-id tombol
if (!$bookingId) {
    $response['message'] = 'ID Pemesanan tidak ada.';
    echo json_encode($response);
    exit;
}

// 2. Dapatkan room_id
$query = $connection->prepare("SELECT room_id FROM orders WHERE id = ?");
$query->bind_param("i", $bookingId);
$query->execute();
$result = $query->get_result();
$booking = $result->fetch_assoc();
$query->close();

if (!$booking) {
    $response['message'] = 'Pemesanan tidak ditemukan.';
    echo json_encode($response);
    exit;
}
$roomId = $booking['room_id'];

// 3. Hapus Pemesanan
$query_delete = $connection->prepare("DELETE FROM orders WHERE id = ?");
$query_delete->bind_param("i", $bookingId);

if ($query_delete->execute()) {
    $query_delete->close();

    // 4. Reset status room
    $query_room = $connection->prepare("UPDATE rooms SET status = 'available' WHERE id = ?");
    $query_room->bind_param("i", $roomId);
    $query_room->execute();
    $query_room->close();

    $response = ['status' => 'success', 'message' => 'Pemesanan berhasil dihapus dan status kamar direset.'];
} else {
    $response['message'] = 'Gagal menghapus pemesanan: ' . $query_delete->error;
}

echo json_encode($response);
exit;
?>