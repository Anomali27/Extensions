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
window.onload = () => Swal.fire({
    icon: 'error',
    title: 'Akses Ditolak!',
    text: 'Anda bukan admin.',
    confirmButtonColor: '#d33'
}).then(() => {
    window.location.href = '../index.php';
});
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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm btnHapus">Hapus</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted">Tidak ada user ditemukan.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane fade" id="bookings" role="tabpanel" aria-labelledby="bookings-tab">
            <table class="table table-hover table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Room</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Duration</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $bookingQuery = $connection->query("
                    SELECT o.id, u.username, r.name as room_name, o.start_date, o.start_time, o.duration, o.status
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    JOIN rooms r ON o.room_id = r.id
                    WHERE o.status IN ('active', 'pending')
                    ORDER BY o.start_date DESC, o.start_time DESC
                    LIMIT 50
                ");
                if ($bookingQuery && $bookingQuery->num_rows > 0):
                  while ($row = $bookingQuery->fetch_assoc()):
                ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['room_name']) ?></td>
                    <td><?= $row['start_date'] ?></td>
                    <td><?= $row['start_time'] ?></td>
                    <td><?= $row['duration'] ?> min</td>
                    <td>
                      <span class="badge bg-<?= $row['status'] == 'active' ? 'success' : ($row['status'] == 'completed' ? 'secondary' : 'warning') ?>">
                        <?= ucfirst($row['status']) ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-info btn-sm btnEditBooking"
                              data-id="<?= $row['id'] ?>"
                              data-date="<?= $row['start_date'] ?>"
                              data-time="<?= $row['start_time'] ?>"
                              data-duration="<?= $row['duration'] ?>">
                        Edit Time
                      </button>
                      <button class="btn btn-warning btn-sm btnCancelBooking" data-id="<?= $row['id'] ?>">
                        Cancel
                      </button>
                      <button class="btn btn-danger btn-sm btnDeleteBooking" data-id="<?= $row['id'] ?>">
                        Delete
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="8" class="text-muted">No active bookings found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane fade" id="rooms" role="tabpanel" aria-labelledby="rooms-tab">

            <div class="row g-4">
              <?php
              $roomQuery = $connection->query("SELECT id, name, type, status FROM rooms ORDER BY id");
              if ($roomQuery && $roomQuery->num_rows > 0):
                while ($row = $roomQuery->fetch_assoc()):
              ?>

              <div class="col-md-3"> <div class="card shadow-sm h-100 room-card" data-room-id="<?= $row['id'] ?>" style="cursor: pointer;">

                  <div class="card-body text-center">
                    <h5 class="card-title fw-bold"><?= htmlspecialchars($row['name']) ?></h5>
                    <p class="text-muted mb-1"><?= htmlspecialchars($row['type']) ?></p>

                    <span class="badge fs-6" style="background-color: <?= $row['status'] == 'available' ? '#28a745' : '#dc3545' ?>; color: white;">
                      <?= ucfirst($row['status']) ?>
                    </span>
                  </div>

                </div>
              </div>

              <?php endwhile; ?>
              <?php else: ?>
                <div class="col-12 text-center text-muted">No rooms found.</div>
              <?php endif; ?>
            </div>

          </div>

          <div class="tab-pane fade" id="inventory" role="tabpanel" aria-labelledby="inventory-tab">
            <table class="table table-hover table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Created At</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // Mengganti $roomQuery dengan query spesifik untuk inventory jika dibutuhkan,
                // namun jika Inventory tab menampilkan data rooms, maka query di atas sudah benar.
                $roomQueryForInventory = $connection->query("SELECT id, name, type, status, created_at FROM rooms ORDER BY id");
                if ($roomQueryForInventory && $roomQueryForInventory->num_rows > 0): ?>
                  <?php while ($room = $roomQueryForInventory->fetch_assoc()): ?>
                    <tr>
                      <td><?= $room['id'] ?></td>
                      <td><?= htmlspecialchars($room['name']) ?></td>
                      <td><?= htmlspecialchars($room['type']) ?></td>
                      <td><?= htmlspecialchars($room['status']) ?></td>
                      <td><?= htmlspecialchars($room['created_at']) ?></td>
                      <td>
                        <button class="btn btn-warning btn-sm btnEditInventory" data-id="<?= $room['id'] ?>">Edit</button>
                        <a href="delete_room.php?id=<?= $room['id'] ?>" class="btn btn-danger btn-sm btnHapus">Delete</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted">No records found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>


          <div class="tab-pane fade" id="topup-history" role="tabpanel" aria-labelledby="topup-history-tab">
            <table class="table table-hover table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Amount</th>
                  <th>Method</th>
                  <th>Status</th>
                  <th>Created At</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($topupHistoryQuery && $topupHistoryQuery->num_rows > 0): ?>
                  <?php while ($topup = $topupHistoryQuery->fetch_assoc()): ?>
                    <tr>
                      <td><?= $topup['id'] ?></td>
                      <td><?= htmlspecialchars($topup['username']) ?></td>
                      <td>Rp <?= number_format($topup['amount'], 0, ',', '.') ?></td>
                      <td><?= htmlspecialchars($topup['method']) ?></td>
                      <td><?= htmlspecialchars($topup['status']) ?></td>
                      <td><?= htmlspecialchars($topup['created_at']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="6" class="text-muted">No records found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

          <div class="tab-pane fade" id="payment-history" role="tabpanel" aria-labelledby="payment-history-tab">
            <table class="table table-hover table-bordered text-center align-middle">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Room</th>
                  <th>Duration</th>
                  <th>Price</th>
                  <th>Status</th>
                  <th>Created At</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($paymentHistoryQuery && $paymentHistoryQuery->num_rows > 0): ?>
                  <?php while ($payment = $paymentHistoryQuery->fetch_assoc()): ?>
                    <tr>
                      <td><?= $payment['id'] ?></td>
                      <td><?= htmlspecialchars($payment['username']) ?></td>
                      <td><?= htmlspecialchars($payment['room_name']) ?></td>
                      <td><?= $payment['duration'] ?> min</td>
                      <td>Rp <?= number_format($payment['price'], 0, ',', '.') ?></td>
                      <td><?= htmlspecialchars($payment['status']) ?></td>
                      <td><?= htmlspecialchars($payment['created_at']) ?></td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="7" class="text-muted">No records found.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>

        </div> </div> </div> <div id="modalAddUser" class="modal">
    <div class="modal-content">
        <h3>Tambah User</h3>
        <form id="addUserForm">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="btn-primary">Tambah</button>
        <button type="button" class="btn-secondary" id="closeModal">Batal</button>
        </form>
    </div>
    </div>

  <div id="modalEditUser" class="modal">
    <div class="modal-content">
      <h3>Edit User</h3>
      <form id="editUserForm" method="POST" action="edit_user.php">
        <input type="hidden" name="id" id="editId">
        <input type="text" name="username" id="editUsername" placeholder="Username" required>
        <input type="email" name="email" id="editEmail" placeholder="Email" required>
        <input type="password" name="password" id="editPassword" placeholder="Password baru (opsional)">
        <select name="status" id="editStatus" required>
          <option value="online">Online</option>
          <option value="offline">Offline</option>
        </select>
        <button type="submit" class="btn-primary">Simpan Perubahan</button>
        <button type="button" class="btn-secondary" id="closeEditModal">Batal</button>
      </form>
    </div>
  </div>

  <div id="modalEditInventory" class="modal">
    <div class="modal-content">
      <h3>Edit Inventory Quantity</h3>
      <form id="editInventoryForm" method="POST" action="edit_inventory.php">
        <input type="hidden" name="id" id="editInventoryId">
        <input type="text" id="editInventoryType" placeholder="Type" readonly>
        <input type="number" name="quantity_available" id="editInventoryQuantity" placeholder="Quantity Available" required min="0">
        <button type="submit" class="btn-primary">Update Quantity</button>
        <button type="button" class="btn-secondary" id="closeEditInventoryModal">Batal</button>
      </form>
    </div>
  </div>

  <div class="modal fade" id="roomModal" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roomModalLabel">Room Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <h6>Room Information</h6>
            <p><strong>Name:</strong> <span id="modalRoomName"></span></p>
            <p><strong>Type:</strong> <span id="modalRoomType"></span></p>
            <p><strong>Status:</strong> <span id="modalRoomStatus" class="badge"></span></p>
          </div>

          <div class="mb-3">
            <h6>Today's Bookings</h6>
            <div id="modalBookings">
              <p class="text-muted">Loading...</p>
            </div>
          </div>

          <div class="mb-3">
            <h6>Available Time Slots (Today)</h6>
            <div id="modalSlots">
              <p class="text-muted">Loading...</p>
            </div>
          </div>

          <div class="mb-3">
            <h6>Actions</h6>
            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-success" id="btnSetAvailable">Set Available</button>
              <button class="btn btn-danger" id="btnSetBooked">Set Booked</button>
              <button class="btn btn-info" id="btnViewHistory">View Booking History</button>
              <button class="btn btn-primary" id="btnEditRoom">Edit Room</button>
              <button class="btn btn-danger" id="btnDeleteRoom">Delete Room</button>
            </div>
          </div>

          <div id="editRoomForm" style="display: none;">
            <h6>Edit Room</h6>
            <form id="formEditRoom">
              <input type="hidden" id="editRoomId">
              <div class="mb-3">
                <label for="editRoomName" class="form-label">Name</label>
                <input type="text" class="form-control" id="editRoomName" required>
              </div>
              <div class="mb-3">
                <label for="editRoomType" class="form-label">Type</label>
                <input type="text" class="form-control" id="editRoomType" required>
              </div>
              <button type="submit" class="btn btn-success">Save Changes</button>
              <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="roomHistoryModal" tabindex="-1" aria-labelledby="roomHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="roomHistoryModalLabel">Booking History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>User</th>
                <th>Date</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="historyTableBody">
              <tr>
                <td colspan="5" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="scriptdashboard.js"></script>

  <script>
  // === SWEETALERT UNTUK PESAN ===
  <?php /*
  <?php if ($successMsg): ?>
      Swal.fire({
          icon: 'success',
          title: 'Berhasil!',
          text: '<?= addslashes($successMsg) ?>',
          confirmButtonColor: '#3085d6',
      });
  <?php elseif ($errorMsg): ?>
      Swal.fire({
          icon: 'error',
          title: 'Oops!',
          text: '<?= addslashes($errorMsg) ?>',
          confirmButtonColor: '#d33',
      });
  <?php endif; ?>
  */ ?>

  // === SWEETALERT UNTUK KONFIRMASI HAPUS ===
  document.querySelectorAll('.btnHapus').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const url = this.getAttribute('href');
      Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data user ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then(result => {
        if (result.isConfirmed) window.location.href = url;
      });
    });
  });


  </script>
</body>
</html>