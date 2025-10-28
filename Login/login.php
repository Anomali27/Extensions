<?php
session_start();
include '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                showModal('Login Berhasil!', true);
                setTimeout(() => window.location.href = '../Dashboard/dashboard.php', 1500);
            });
        </script>";
    } else {
        echo "<script>
            document.addEventListener('DOMContentLoaded', () => {
                showModal('Username atau password salah!', false);
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login - PS Billing</title>
  <link rel="stylesheet" href="stylelogin.css">
</head>
<body>
<div class="container">
  <div class="left-panel">
    <img src="../assets/Logo.png" alt="Logo" class="logo">
    <h2>Masuk Akun</h2>
    <form method="POST">
      <input type="text" name="username" placeholder="Username" required>
      <input type="password" name="password" placeholder="Kata Sandi" required>
      <button type="submit">Login</button>
    </form>
    <p>Belum punya akun? <a href="../Sign-Up/signup.php" class="switch">Daftar</a></p>
  </div>

  <div class="right-panel">
    <h2>Selamat Datang Kembali!</h2>
    <p>Masuk ke akun Anda untuk mengakses layanan billing PlayStation.</p>
  </div>
</div>

<!-- Modal -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span id="modal-message"></span>
  </div>
</div>

<script src="scriptlogin.js"></script>
</body>
</html>
