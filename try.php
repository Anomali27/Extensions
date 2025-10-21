<?php
// Koneksi database (sama)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "playstation-biling";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Data sebelumnya (user dan inventory)
$userQuery = "SELECT COUNT(*) AS total_users FROM users";
$userResult = $conn->query($userQuery);
$totalUsers = $userResult->fetch_assoc()['total_users'];

$inventoryQuery = "SELECT type, quantity_available FROM inventory";
$inventoryResult = $conn->query($inventoryQuery);
$inventory = [];
while ($row = $inventoryResult->fetch_assoc()) {
    $inventory[$row['type']] = $row['quantity_available'];
}

// Tambahan: Jumlah pemesanan aktif
$orderQuery = "SELECT COUNT(*) AS total_orders FROM orders WHERE status = 'pending'";
$orderResult = $conn->query($orderQuery);
$totalOrders = $orderResult->fetch_assoc()['total_orders'];

$conn->close();

$data = [
    'totalUsers' => $totalUsers,
    'ps4' => $inventory['PS4'] ?? 0,
    'ps5' => $inventory['PS5'] ?? 0,
    'vr' => $inventory['VR'] ?? 0,
    'totalOrders' => $totalOrders
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemesanan Billing PlayStation</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f4; }
        .dashboard { display: flex; flex-wrap: wrap; gap: 20px; }
        .card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); flex: 1; min-width: 200px; }
        h1 { text-align: center; color: #333; }
        canvas { max-width: 100%; }
    </style>
</head>
<body>
    <h1>Dashboard Layanan Pemesanan Billing PlayStation</h1>
    <div class="dashboard">
        <div class="card">
            <h2>Jumlah User</h2>
            <p id="totalUsers">Loading...</p>
        </div>
        <div class="card">
            <h2>Pemesanan Aktif</h2>
            <p id="totalOrders">Loading...</p>
        </div>
        <div class="card">
            <h2>Stok Unit</h2>
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <script>
        const data = <?php echo json_encode($data); ?>;
        document.getElementById('totalUsers').textContent = data.totalUsers;
        document.getElementById('totalOrders').textContent = data.totalOrders;

        const ctx = document.getElementById('inventoryChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['PS4', 'PS5', 'VR'],
                datasets: [{
                    label: 'Jumlah Tersedia',
                    data: [data.ps4, data.ps5, data.vr],
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    borderColor: ['#FF6384', '#36A2EB', '#FFCE56'],
                    borderWidth: 1
                }]
            },
            options: { scales: { y: { beginAtZero: true } } }
        });
    </script>
</body>
</html>
