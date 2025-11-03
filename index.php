<?php
session_start();
include './config/config.php';

// Cek login
$isLoggedIn = isset($_SESSION['user']);
$username = $isLoggedIn ? $_SESSION['user'] : null;

// Ambil role user (admin / user)
$role = null;
if ($isLoggedIn) {
    $query = $connection->prepare("SELECT role FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();
    if ($row = $result->fetch_assoc()) {
        $role = $row['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PS Billing</title>
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">
            <a href="index.php">
                <img src="./assets/Logo.png" alt="Logo" />
            </a>
        </div>

        <nav>
            <ul>
                <li><a href="#home">Beranda</a></li>
                <li><a href="#unit">Unit Tersedia</a></li>
                <li><a href="#paket">Paket Harga</a></li>
                <?php if ($role === 'admin'): ?>
                    <li><a href="./Dashboard/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="user-area">
            <?php if ($isLoggedIn): ?>
                <div class="profile">
                    <img src="./assets/user.png" alt="User" class="avatar" />
                    <span><?php echo htmlspecialchars($username); ?></span>
                    <?php if ($role === 'admin'): ?>
                        <span class="admin-badge">Admin</span>
                    <?php endif; ?>
                    <a href="./auth/logout.php" class="logout-btn">Logout</a>
                </div>
            <?php else: ?>
                <a href="./auth/auth.php" class="btn login">Login</a>
                <a href="./auth/auth.php" class="btn signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- HERO -->
    <section id="home" class="hero">
        <div class="hero-text">
            <h2>Selamat datang di <span>Extension</span></h2>
            <p>Tempat terbaik untuk bermain dan mengelola PlayStation favoritmu.</p>
        </div>
        <div class="hero-img">
            <img src="./assets/Playstationnobg.png" alt="PS" />
        </div>
    </section>

    <!-- UNIT -->
    <section id="unit" class="unit section">
        <h2>Unit Tersedia</h2>
        <div class="unit-grid">
            <div class="unit-card">
                <img src="./assets/PS5.png" alt="PS5" />
                <h3>PlayStation 5</h3>
                <p>Kualitas grafis 4K, performa cepat, dan pengalaman generasi terbaru.</p>
                <a href="./Unit/unit.php" class="unit-btn">Lihat Detail</a>
            </div>

            <div class="unit-card">
                <img src="./assets/PS4.png" alt="PS4" />
                <h3>PlayStation 4 Pro</h3>
                <p>Performa solid dan cocok untuk multiplayer bersama teman-teman.</p>
                <a href="./Unit/unit.php" class="unit-btn">Lihat Detail</a>
            </div>

            <div class="unit-card">
                <img src="./assets/VR.png" alt="VR" />
                <h3>PS VR Set</h3>
                <p>Rasakan dunia virtual dengan pengalaman bermain yang imersif.</p>
                <a href="./Unit/unit.php" class="unit-btn">Lihat Detail</a>
            </div>
        </div>
    </section>

    <!-- PAKET -->
    <section id="paket" class="paket section">
      <h2>Paket Harga</h2>
      <div class="paket-grid">

        <div class="paket-card">
          <h3>Paket Reguler</h3>
          <p>1 Jam Bermain</p>
          <span>Rp 10.000</span>
          <a href="#" class="paket-btn">Pesan Sekarang</a>
        </div>

        <div class="paket-card">
          <h3>Paket Hemat</h3>
          <p>3 Jam Bermain</p>
          <span>Rp 25.000</span>
          <a href="#" class="paket-btn">Pesan Sekarang</a>
        </div>

        <div class="paket-card">
          <h3>Paket Full</h3>
          <p>6 Jam Bermain</p>
          <span>Rp 45.000</span>
          <a href="#" class="paket-btn">Pesan Sekarang</a>
        </div>

        <div class="paket-card">
          <h3>Paket Malam</h3>
          <p>Mulai 22.00 - 06.00</p>
          <span>Rp 60.000</span>
          <a href="#" class="paket-btn">Pesan Sekarang</a>
        </div>

        <div class="paket-card">
          <h3>Paket VIP</h3>
          <p>12 Jam + Snack & Minuman Gratis</p>
          <span>Rp 90.000</span>
          <a href="#" class="paket-btn">Pesan Sekarang</a>
        </div>

      </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>© 2025 PS Billing. All Rights Reserved.</p>
    </footer>

    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="script.js"></script>
</body>
</html>
