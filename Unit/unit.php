<?php
session_start();
include '../config/config.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header('Location: ../auth/auth.php');
    exit;
}

$username = $_SESSION['user'];

// Ambil role user
$query = $connection->prepare("SELECT role FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$role = $result->fetch_assoc()['role'];

// Ambil data rooms dari database (asumsi tabel rooms ada)
$rooms = [];
$query = $connection->query("SELECT * FROM rooms ORDER BY type, id");
while ($row = $query->fetch_assoc()) {
    $rooms[] = $row;
}


?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unit Tersedia - PS Billing</title>
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
                <li><a href="#unit">Unit Tersedia</a></li>
                <li><a href="../index.php#paket">Paket Harga</a></li>
                <li><a href="orders.php">Pesanan Saya</a></li>
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

    <!-- UNIT ROOMS -->
    <section id="unit" class="unit section">

        <div class="unit-types">
            <!-- PlayStation 5 Section -->
            <div class="unit-category">
                <div class="category-header">
                    <div class="category-icon">
                        <img src="../assets/PS5.png" alt="PS5" class="console-icon">
                    </div>
                    <div class="category-info">
                        <h3>PlayStation 5</h3>
                        <p>Experience next-gen gaming with ultra-fast SSD and stunning graphics</p>
                    </div>
                </div>
                <div class="rooms-grid">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room['type'] == 'PS5'): ?>
                            <div class="room-card <?php echo $room['status'] == 'booked' ? 'booked' : 'available'; ?>" data-room-id="<?php echo $room['id']; ?>" data-type="PS5" data-room-name="<?php echo $room['name']; ?>">
                                <div class="room-icon">
                                    <img src="../assets/PS5.png" alt="PS5" class="room-console-icon">
                                </div>
                                <h4><?php echo $room['name']; ?></h4>
                                <div class="room-status">
                                    <span class="status-indicator <?php echo $room['status']; ?>"></span>
                                    <p><?php echo $room['status'] == 'available' ? 'Tersedia' : 'Dipesan'; ?></p>
                                </div>
                                <div class="room-features">
                                    <span class="feature-tag">4K Gaming</span>
                                    <span class="feature-tag">Ray Tracing</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- PlayStation 4 Section -->
            <div class="unit-category">
                <div class="category-header">
                    <div class="category-icon">
                        <img src="../assets/PS4.png" alt="PS4" class="console-icon">
                    </div>
                    <div class="category-info">
                        <h3>PlayStation 4</h3>
                        <p>Classic gaming experience with thousands of games available</p>
                    </div>
                </div>
                <div class="rooms-grid">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room['type'] == 'PS4'): ?>
                            <div class="room-card <?php echo $room['status'] == 'booked' ? 'booked' : 'available'; ?>" data-room-id="<?php echo $room['id']; ?>" data-type="PS4" data-room-name="<?php echo $room['name']; ?>">
                                <div class="room-icon">
                                    <img src="../assets/PS4.png" alt="PS4" class="room-console-icon">
                                </div>
                                <h4><?php echo $room['name']; ?></h4>
                                <div class="room-status">
                                    <span class="status-indicator <?php echo $room['status']; ?>"></span>
                                    <p><?php echo $room['status'] == 'available' ? 'Tersedia' : 'Dipesan'; ?></p>
                                </div>
                                <div class="room-features">
                                    <span class="feature-tag">1080p</span>
                                    <span class="feature-tag">HDR</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- VR Section -->
            <div class="unit-category">
                <div class="category-header">
                    <div class="category-icon">
                        <img src="../assets/VR.png" alt="VR" class="console-icon">
                    </div>
                    <div class="category-info">
                        <h3>Virtual Reality</h3>
                        <p>Immerse yourself in virtual worlds with cutting-edge VR technology</p>
                    </div>
                </div>
                <div class="rooms-grid">
                    <?php foreach ($rooms as $room): ?>
                        <?php if ($room['type'] == 'VR'): ?>
                            <div class="room-card <?php echo $room['status'] == 'booked' ? 'booked' : 'available'; ?>" data-room-id="<?php echo $room['id']; ?>" data-type="VR" data-room-name="<?php echo $room['name']; ?>">
                                <div class="room-icon">
                                    <img src="../assets/VR.png" alt="VR" class="room-console-icon">
                                </div>
                                <h4><?php echo $room['name']; ?></h4>
                                <div class="room-status">
                                    <span class="status-indicator <?php echo $room['status']; ?>"></span>
                                    <p><?php echo $room['status'] == 'available' ? 'Tersedia' : 'Dipesan'; ?></p>
                                </div>
                                <div class="room-features">
                                    <span class="feature-tag">VR Ready</span>
                                    <span class="feature-tag">Motion Control</span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- BOOKING MODAL -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Pesan Room <span id="modalRoomType"></span> - <span id="modalRoomName"></span></h2>
            <form id="bookingForm">
                <input type="hidden" id="roomId" name="roomId">

                <label for="duration">Durasi (jam):</label>
                <div class="duration-controls">
                    <button type="button" id="decreaseDuration">-</button>
                    <input type="number" id="duration" name="duration" min="1" step="0.5" value="1" required>
                    <button type="button" id="increaseDuration">+</button>
                </div>

                <div class="packages">
                    <h3>Pilih Paket atau Custom:</h3>
                    <div class="package-options">
                        <label><input type="radio" name="package" value="Reguler"> Paket Reguler - 1 Jam - Rp 10.000</label>
                        <label><input type="radio" name="package" value="Hemat"> Paket Hemat - 3 Jam - Rp 25.000</label>
                        <label><input type="radio" name="package" value="Full"> Paket Full - 6 Jam - Rp 45.000</label>
                        <label><input type="radio" name="package" value="Malam"> Paket Malam - 8 Jam - Rp 60.000</label>
                        <label><input type="radio" name="package" value="VIP"> Paket VIP - 12 Jam - Rp 90.000</label>
                        <label><input type="radio" name="package" value="Custom" checked> Custom</label>
                    </div>
                </div>

                <label for="startDate">Tanggal:</label>
                <input type="date" id="startDate" name="startDate" required>

                <label for="startTime">Jam Mulai:</label>
                <div class="time-selection">
                    <button type="button" class="time-btn" data-time="00:00">00:00</button>
                    <button type="button" class="time-btn" data-time="01:00">01:00</button>
                    <button type="button" class="time-btn" data-time="02:00">02:00</button>
                    <button type="button" class="time-btn" data-time="03:00">03:00</button>
                    <button type="button" class="time-btn" data-time="04:00">04:00</button>
                    <button type="button" class="time-btn" data-time="05:00">05:00</button>
                    <button type="button" class="time-btn" data-time="06:00">06:00</button>
                    <button type="button" class="time-btn" data-time="07:00">07:00</button>
                    <button type="button" class="time-btn" data-time="08:00">08:00</button>
                    <button type="button" class="time-btn" data-time="09:00">09:00</button>
                    <button type="button" class="time-btn" data-time="10:00">10:00</button>
                    <button type="button" class="time-btn" data-time="11:00">11:00</button>
                    <button type="button" class="time-btn" data-time="12:00">12:00</button>
                    <button type="button" class="time-btn" data-time="13:00">13:00</button>
                    <button type="button" class="time-btn" data-time="14:00">14:00</button>
                    <button type="button" class="time-btn" data-time="15:00">15:00</button>
                    <button type="button" class="time-btn" data-time="16:00">16:00</button>
                    <button type="button" class="time-btn" data-time="17:00">17:00</button>
                    <button type="button" class="time-btn" data-time="18:00">18:00</button>
                    <button type="button" class="time-btn" data-time="19:00">19:00</button>
                    <button type="button" class="time-btn" data-time="20:00">20:00</button>
                    <button type="button" class="time-btn" data-time="21:00">21:00</button>
                    <button type="button" class="time-btn" data-time="22:00">22:00</button>
                    <button type="button" class="time-btn" data-time="23:00">23:00</button>
                </div>
                <input type="hidden" id="startTime" name="startTime" required>

                <div class="total-price-container">
                    <p>Total Harga: Rp <span id="totalPrice">0</span></p>
                </div>
                <div class="button-container">
                    <button type="submit">Pesan & Bayar</button>
                </div>
            </form>
        </div>
    </div>

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
