<?php
session_start();

// Jika di localhost, hancurkan session agar tidak ada akun yang terlogin
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    session_destroy();
    session_start();
}

include '../config/config.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/auth.php');
    exit;
}

$username = $_SESSION['user'];
$user_id = $_SESSION['user_id'];

// Fetch user data with saldo
$query = "SELECT id, username, email, saldo FROM users WHERE id = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Top Up Saldo - PS Billing</title>
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="topup.css" />
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
                <li><a href="../index.php#unit">Unit Tersedia</a></li>
                <li><a href="../index.php#paket">Paket Harga</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="../Unit/orders.php">Pesanan Saya</a></li>
                <?php endif; ?>
                <?php
                $role = null;
                if (isset($_SESSION['user'])) {
                    $query = $connection->prepare("SELECT role FROM users WHERE username = ?");
                    $query->bind_param("s", $_SESSION['user']);
                    $query->execute();
                    $result = $query->get_result();
                    if ($row = $result->fetch_assoc()) {
                        $role = $row['role'];
                    }
                }
                if ($role === 'admin'): ?>
                    <li><a href="../Dashboard/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="user-area">
            <?php if (isset($_SESSION['user'])): ?>
                <div class="profile">
                    <img src="../assets/user.png" alt="User" class="avatar" />
                    <span><?php echo htmlspecialchars($username); ?></span>
                    <?php if ($role === 'admin'): ?>
                        <span class="admin-badge">Admin</span>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="logout-btn">Logout</a>
                </div>
            <?php else: ?>
                <a href="../auth/auth.php" class="btn login">Login</a>
                <a href="../auth/auth.php" class="btn signup">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- TOPUP SECTION -->
    <section class="topup-section">
        <div class="topup-container">
            <h2>Top Up Saldo</h2>

            <div class="current-balance">
                <h3>Current Balance</h3>
                <div class="balance-amount">Rp <?php echo number_format($user['saldo'], 0, ',', '.'); ?></div>
            </div>

            <form id="topupForm" class="topup-form">
                <div class="form-group">
                    <label for="amount">Select Amount</label>
                    <div class="amount-options">
                        <button type="button" class="amount-btn" data-amount="5000">Rp 5.000</button>
                        <button type="button" class="amount-btn" data-amount="10000">Rp 10.000</button>
                        <button type="button" class="amount-btn" data-amount="20000">Rp 20.000</button>
                        <button type="button" class="amount-btn" data-amount="50000">Rp 50.000</button>
                        <button type="button" class="amount-btn" data-amount="100000">Rp 100.000</button>
                    </div>
                    <input type="number" id="customAmount" placeholder="Or enter custom amount (min 5000)" min="5000" />
                </div>

                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="method-options">
                        <label class="method-option">
                            <input type="radio" name="method" value="QRIS" />
                            <span>QRIS</span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="method" value="Dana" />
                            <span>Dana</span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="method" value="OVO" />
                            <span>OVO</span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="method" value="GoPay" />
                            <span>GoPay</span>
                        </label>
                        <label class="method-option">
                            <input type="radio" name="method" value="Bank Transfer" />
                            <span>Bank Transfer</span>
                        </label>
                    </div>
                </div>

                <button type="submit" class="pay-btn">Pay Now</button>
            </form>
        </div>
    </section>

    <footer>
        <p>© 2025 PS Billing. All Rights Reserved.</p>
    </footer>

    <script src="topup.js"></script>
</body>
</html>
