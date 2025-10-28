<?php
session_start();
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Cek apakah username sudah digunakan
    $check = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                showModal('Username sudah digunakan!', false);
            });
        </script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $username, $email, $password);
        if ($stmt->execute()) {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    showModal('Akun berhasil dibuat!', true);
                    setTimeout(() => window.location.href = '../Login/login.php', 1500);
                });
            </script>";
        } else {
            echo "<script>
                document.addEventListener('DOMContentLoaded', () => {
                    showModal('Terjadi kesalahan, coba lagi!', false);
                });
            </script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - PS Billing</title>
  <link rel="stylesheet" href="stylesignup.css">
</head>
<body>
<div class="container">
  <div class="left-panel">
    <h2>Selamat Datang di PS Billing!</h2>
    <p>Daftarkan akun Anda dan nikmati layanan pemesanan PlayStation dengan mudah.</p>
  </div>

  <div class="right-panel">
    <img src="../assets/Logo.png" alt="Logo" class="logo">
    <h2>Buat Akun</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Kata Sandi" required>
      <button type="submit">Sign Up</button>
    </form>
    <p>Sudah punya akun? <a href="../Login/login.php" class="switch">Login</a></p>
  </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span id="modal-message"></span>
  </div>
</div>

<script src="scriptsignup.js"></script>
</body>
</html>
