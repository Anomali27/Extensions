<?php
session_start();
include '../config/config.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/auth.php');
    exit;
}

$username = $_SESSION['user'];

// Ambil user_id
$query = $connection->prepare("SELECT id, role FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();
$user_id = $user['id'];
$role = $user['role'];

// Ambil orders user
$bookings = [];
$query = $connection->prepare("
    SELECT b.*, r.type, r.name AS room_number
    FROM orders b
    JOIN rooms r ON b.room_id = r.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Saya - PS Billing</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="styleunit.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">
            <a href="../index.php">
                <img src="../assets/Logo.png" alt="Logo" />
            </a>
        </div>

        <nav>
            <ul>
                <li><a href="../index.php#home">Beranda</a></li>
                <li><a href="unit.php">Unit Tersedia</a></li>
                <li><a href="../index.php#paket">Paket Harga</a></li>
                <li><a href="#orders">Pesanan Saya</a></li>
                <?php if ($role === 'admin'): ?>
                    <li><a href="../Dashboard/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="user-area">
            <div class="profile">
                <img src="../assets/user.png" alt="User" class="avatar" />
                <span><?php echo htmlspecialchars($username); ?></span>
                <?php if ($role === 'admin'): ?>
                    <span class="admin-badge">Admin</span>
                <?php endif; ?>
                <a href="../auth/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <!-- ORDERS -->
    <section id="orders" class="orders section">
        <h2>Pesanan Saya</h2>
        <?php if (empty($bookings)): ?>
            <p>Anda belum memiliki pesanan.</p>
        <?php else: ?>
            <div class="orders-grid">
                <?php foreach ($bookings as $booking): ?>
                    <div class="order-card">
                        <h3><?php echo $booking['type']; ?> - Room <?php echo $booking['room_number']; ?></h3>
                        <p>Tanggal: <?php echo date('d/m/Y', strtotime($booking['start_date'])); ?></p>
                        <p>Jam Mulai: <?php echo $booking['start_time']; ?></p>
                        <p>Durasi: <?php echo $booking['duration']; ?> menit</p>
                        <p>Paket: <?php echo $booking['package'] ?: 'Custom'; ?></p>
                        <p>Harga: Rp <?php echo number_format($booking['price'], 0, ',', '.'); ?></p>
                        <p>Status: <?php echo ucfirst($booking['status']); ?></p>
                        <?php if ($booking['status'] == 'active'): ?>
                            <div class="stopwatch" data-booking-id="<?php echo $booking['id']; ?>" data-duration="<?php echo $booking['duration']; ?>">
                                <div class="time-display">
                                    <div class="time-label">Waktu Tersisa</div>
                                    <div class="time-remaining">--:--:--</div>
                                </div>
                                <button class="start-btn" onclick="startSession(<?php echo $booking['id']; ?>)">Mulai</button>
                                <?php if ($role === 'admin'): ?>
                                    <button class="stop-btn" onclick="stopSession(<?php echo $booking['id']; ?>)">Stop</button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2025 PS Billing. All Rights Reserved.</p>
    </footer>

    <script src="scriptunit.js"></script>
    <script>
        // Auto-complete check every minute
        setInterval(() => {
            fetch('auto_complete.php')
                .then(response => response.text())
                .then(data => console.log('Auto complete check:', data))
                .catch(error => console.error('Auto complete error:', error));
        }, 60000); // Every 60 seconds
    </script>
</body>
</html>
