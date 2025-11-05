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

    <!-- Statistik -->
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

    <!-- Daftar User -->
    <div class="user-section p-4 shadow-sm rounded bg-white">
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

      <!-- Tab Navigation -->
      <ul class="nav nav-tabs mb-3" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">Users</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button" role="tab">Bookings</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="rooms-tab" data-bs-toggle="tab" data-bs-target="#rooms" type="button" role="tab">Rooms</button>
        </li>
      </ul>

      <!-- Tab Content -->
      <div class="tab-content" id="dashboardTabsContent">
        <!-- Users Tab -->
        <div class="tab-pane fade show active" id="users" role="tabpanel">

      <table class="table table-hover table-bordered text-center align-middle">
        <thead class="table-dark">
          <tr>
            <th style="width: 60px;">ID</th>
            <th>Username</th>
            <th>Email</th>
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
              <td>
                <span class="badge <?= ($row['status'] ?? 'offline') == 'online' ? 'bg-success' : 'bg-secondary' ?>">
                  <?= ucfirst($row['status'] ?? 'offline') ?>
                </span>
              </td>
              <td>
                <!-- Tombol Edit diperbarui agar buka modal -->
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
          <tr><td colspan="5" class="text-muted">Tidak ada user ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
        </div>

        <!-- Bookings Tab -->
        <div class="tab-pane fade" id="bookings" role="tabpanel">
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
                    <button class="btn btn-danger btn-sm btnCancelBooking" data-id="<?= $row['id'] ?>">
                      Cancel
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="8" class="text-muted">No bookings found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Rooms Tab -->
        <div class="tab-pane fade" id="rooms" role="tabpanel">
          <table class="table table-hover table-bordered text-center align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Type</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $roomQuery = $connection->query("SELECT id, name, type, status FROM rooms ORDER BY id");
              if ($roomQuery && $roomQuery->num_rows > 0):
                while ($row = $roomQuery->fetch_assoc()):
              ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= htmlspecialchars($row['name']) ?></td>
                  <td><?= htmlspecialchars($row['type']) ?></td>
                  <td>
                    <span class="badge bg-<?= $row['status'] == 'available' ? 'success' : ($row['status'] == 'booked' ? 'warning' : 'danger') ?>">
                      <?= ucfirst($row['status']) ?>
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-warning btn-sm btnToggleRoom"
                            data-id="<?= $row['id'] ?>"
                            data-status="<?= $row['status'] ?>">
                      <?= $row['status'] == 'available' ? 'Set Booked' : 'Set Available' ?>
                    </button>
                  </td>
                </tr>
              <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-muted">No rooms found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Tambah User -->
    <div id="modalAddUser" class="modal">
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

  <!-- Modal Edit User -->
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="scriptdashboard.js"></script>

  <script>
  // === SWEETALERT UNTUK PESAN ===
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

  // === BOOKING MANAGEMENT ===
  // Edit Booking Time
  document.querySelectorAll('.btnEditBooking').forEach(btn => {
    btn.addEventListener('click', function() {
      const bookingId = this.dataset.id;
      const currentDate = this.dataset.date;
      const currentTime = this.dataset.time;
      const currentDuration = this.dataset.duration;

      Swal.fire({
        title: 'Edit Booking Time',
        html: `
          <input type="date" id="newDate" class="swal2-input" value="${currentDate}" min="${new Date().toISOString().split('T')[0]}">
          <input type="time" id="newTime" class="swal2-input" value="${currentTime}">
          <input type="number" id="newDuration" class="swal2-input" value="${currentDuration / 60}" min="0.5" step="0.5" placeholder="Duration in hours">
        `,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
          const newDate = document.getElementById('newDate').value;
          const newTime = document.getElementById('newTime').value;
          const newDuration = document.getElementById('newDuration').value;

          if (!newDate || !newTime || !newDuration) {
            Swal.showValidationMessage('All fields are required');
            return false;
          }

          return { newDate, newTime, newDuration };
        }
      }).then(result => {
        if (result.isConfirmed) {
          fetch('update_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              bookingId: bookingId,
              newDate: result.value.newDate,
              newTime: result.value.newTime,
              newDuration: result.value.newDuration * 60
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Success', 'Booking updated successfully', 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    });
  });

  // Cancel Booking
  document.querySelectorAll('.btnCancelBooking').forEach(btn => {
    btn.addEventListener('click', function() {
      const bookingId = this.dataset.id;

      Swal.fire({
        title: 'Cancel Booking?',
        text: 'This will cancel the booking permanently.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel',
        cancelButtonText: 'No'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('cancel_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ bookingId: bookingId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Success', 'Booking cancelled successfully', 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    });
  });

  // === ROOM MANAGEMENT ===
  // Toggle Room Status
  document.querySelectorAll('.btnToggleRoom').forEach(btn => {
    btn.addEventListener('click', function() {
      const roomId = this.dataset.id;
      const currentStatus = this.dataset.status;
      const newStatus = currentStatus === 'available' ? 'booked' : 'available';

      Swal.fire({
        title: `Set Room ${newStatus}?`,
        text: `This will change the room status to ${newStatus}.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
      }).then(result => {
        if (result.isConfirmed) {
          fetch('toggle_room.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roomId: roomId, newStatus: newStatus })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire('Success', `Room status updated to ${newStatus}`, 'success').then(() => location.reload());
            } else {
              Swal.fire('Error', data.message, 'error');
            }
          });
        }
      });
    });
  });
  </script>
</body>
</html>
