<?php
require_once __DIR__ . '/../config/config.php';
session_start();

// 🔒 Proteksi hanya admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo "<!doctype html>
<html lang='id'>
<head>
  <meta charset='utf-8'>
  <meta name='viewport' content='width=device-width,initial-scale=1'>
  <title>Akses Ditolak</title>
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
</head>
<body>
<script>
</script>
</body>
</html>";
    exit;
}

// --- Inisialisasi ---
$totalUser = 0;
$userOnline = 0;
$activeOrders = 0;
$stok = 0;
$inventory = [];

// Ambil total user
$q = $connection->query("SELECT COUNT(*) AS total FROM users");
if ($q) $totalUser = (int)$q->fetch_assoc()['total'];

// Ambil user online
$q = $connection->query("SELECT COUNT(*) AS total FROM users WHERE status = 'online'");
if ($q) $userOnline = (int)$q->fetch_assoc()['total'];

// Ambil pemesanan aktif
$q = $connection->query("SELECT COUNT(*) AS total FROM orders WHERE status = 'pending'");
if ($q) $activeOrders = (int)$q->fetch_assoc()['total'];

// Ambil total stok
$q = $connection->query("SELECT COALESCE(SUM(quantity_available), 0) AS total FROM inventory");
if ($q) $stok = (int)$q->fetch_assoc()['total'];

// Ambil inventory detail
$q = $connection->query("SELECT type, quantity_available FROM inventory");
if ($q) {
    while ($r = $q->fetch_assoc()) {
        $inventory[$r['type']] = (int)$r['quantity_available'];
    }
}

// Pencarian user
$search = isset($_GET['search']) ? $connection->real_escape_string($_GET['search']) : '';
$userQuery = $connection->query("SELECT * FROM users WHERE username LIKE '%$search%' OR email LIKE '%$search%' ORDER BY id DESC");

// Query for Inventory tab - rooms data (Ini tetap ada karena digunakan di tab "Inventory" di atas)
$roomQuery = $connection->query("SELECT id, name, type, status, created_at FROM rooms ORDER BY id");

// Query for Top Up History tab - topup_history joined with users, filter status 'success' or 'failed'
$topupHistoryQuery = $connection->query("
    SELECT th.id, u.username, th.amount, th.method, th.status, th.created_at
    FROM topup_history th
    JOIN users u ON th.user_id = u.id
    WHERE th.status IN ('success', 'failed')
    ORDER BY th.created_at DESC
");

// Query for Payment History tab - orders joined with users and rooms
$paymentHistoryQuery = $connection->query("
    SELECT o.id, u.username, r.name as room_name, o.duration, o.price, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN rooms r ON o.room_id = r.id
    ORDER BY o.created_at DESC
");

// Ambil pesan dari session
$successMsg = $_SESSION['success_message'] ?? null;
$errorMsg = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard Pemesanan Billing PlayStation</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="..." crossorigin="anonymous">

  <link rel="stylesheet" href="styledashboard.css">
  <style>
    /* Fix to allow nav tabs clickable despite modal shown */
    .nav-tabs {
      position: relative;
      z-index: 1060; /* higher than modal backdrop default 1050 */
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

  <div class="container mt-5">
    <h1 class="text-center mb-4">
      <div class="Homepage">
        <a href="../../index.php" class="logo-link">
          <img src="../assets/Logo.png" alt="logo" class="logo"/>
        </a> Dashboard Pemesanan Billing PlayStation
      </div>
    </h1>

    <div class="row text-center g-4 mb-4">
      <div class="col-md-3">
        <div class="card info-card p-4 shadow-sm">
          <h6 class="text-secondary mb-2">Jumlah User</h6>
          <h2 class="fw-bold text-dark"><?= $totalUser ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card info-card p-4 shadow-sm">
          <h6 class="text-secondary mb-2">User Online</h6>
          <h2 class="fw-bold text-success"><?= $userOnline ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card info-card p-4 shadow-sm">
          <h6 class="text-secondary mb-2">Pemesanan Aktif</h6>
          <h2 class="fw-bold text-primary"><?= $activeOrders ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card info-card p-4 shadow-sm">
          <h6 class="text-secondary mb-2">Total Stok Unit</h6>
          <h2 class="fw-bold text-dark"><?= $stok ?></h2>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-3">
        <ul class="nav nav-tabs flex-column" id="dashboardTabs" role="tablist" aria-orientation="vertical">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Users</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab" aria-controls="bookings" aria-selected="false">Bookings</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab" aria-controls="rooms" aria-selected="false">Rooms</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="inventory-tab" data-bs-toggle="tab" data-bs-target="#inventory" type="button" role="tab" aria-controls="inventory" aria-selected="false">Inventory</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="topup-history-tab" data-bs-toggle="tab" data-bs-target="#topup-history" type="button" role="tab" aria-controls="topup-history" aria-selected="false">Top Up History</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="payment-history-tab" data-bs-toggle="tab" data-bs-target="#payment-history" type="button" role="tab" aria-controls="payment-history" aria-selected="false">Payment History</button>
          </li>
        </ul>
      </div>
      <div class="col-md-9">
        <div class="tab-content" id="dashboardTabsContent">
          <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">

            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <h4 class="m-0">👥 Daftar User</h4>
              <div class="d-flex gap-2">
                <form method="GET" class="d-flex">
                  <input type="text" name="search" class="form-control me-2" placeholder="Cari user..." value="<?= htmlspecialchars($search) ?>">
                  <button class="btn btn-primary">Cari</button>
                </form>
                <button class="btn btn-success" id="btnAddUser">+ Tambah User</button>
              </div>
            </div>

            <table class="table table-hover table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th style="width: 60px;">ID</th>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Saldo</th>
                  <th>Status</th>
                  <th style="width: 160px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($userQuery && $userQuery->num_rows > 0): ?>
                  <?php while ($row = $userQuery->fetch_assoc()): ?>
                    <tr>
                      <td><?= $row['id'] ?></td>
                      <td><?= htmlspecialchars($row['username']) ?></td>
                      <td><?= htmlspecialchars($row['email']) ?></td>
                      <td>Rp <?= number_format($row['saldo'] ?? 0, 0, ',', '.') ?></td>
                      <td>
                        <span class="badge <?= ($row['status'] ?? 'offline') == 'online' ? 'bg-success' : 'bg-secondary' ?>">
                          <?= ucfirst($row['status'] ?? 'offline') ?>
                        </span>
                      </td>
                      <td>
                        <button
                          class="btn btn-warning btn-sm btnEditUser"
                          data-id="<?= $row['id'] ?>"
                          data-username="<?= htmlspecialchars($row['username']) ?>"
                          data-email="<?= htmlspecialchars($row['email']) ?>"
                          data-status="<?= $row['status'] ?>">
                          Edit
                        </button>
                        <a href="delete_user.php?id=<?= $row['id'] ?>"
                           class="btn btn-danger btn-sm btnDeleteUserLink"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus user <?= htmlspecialchars($row['username']) ?>?');">
                          Hapus
                        </a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted">Tidak ada user ditemukan.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <!-- Other tabs remain unchanged, omitted here for brevity -->

        </div>
      </div>
    </div>
  </div>

  <style>
    /* Fix to allow nav tabs clickable despite modal shown */
    .nav-tabs {
      position: relative;
      z-index: 1050; /* set lower than modal backdrop */
    }
    .modal-backdrop {
      z-index: 1060 !important; /* above nav-tabs */
    }
    .modal {
      z-index: 1070 !important; /* above backdrop and nav-tabs */
    }
  </style>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <div id="alertData" data-success-message="<?= htmlspecialchars($successMsg ?? '') ?>" data-error-message="<?= htmlspecialchars($errorMsg ?? '') ?>"></div>

  <?php include __DIR__ . '/user_modals.php'; ?>
  <?php include __DIR__ . '/rooms_modals.php'; ?>
  <?php include __DIR__ . '/inventory_modals.php'; ?>
  <?php include __DIR__ . '/booking_modals.php'; ?>

  <script src="scriptdashboard.js"></script>
</body>
</html>
