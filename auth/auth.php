<?php
include '../config/config.php';
session_start();

// ==== PROSES LOGIN ====
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            echo "<script>
                    window.onload = () => Swal.fire({
                        icon: 'success',
                        title: 'Login berhasil!',
                        text: 'Selamat datang, " . $user['username'] . "!',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '../../index.php';
                    });
                  </script>";
        } else {
            echo "<script>
                    window.onload = () => Swal.fire({
                        icon: 'error',
                        title: 'Password salah!',
                        text: 'Silakan coba lagi.'
                    });
                  </script>";
        }
    } else {
        echo "<script>
                window.onload = () => Swal.fire({
                    icon: 'error',
                    title: 'Email tidak ditemukan!',
                    text: 'Pastikan email sudah terdaftar.'
                });
              </script>";
    }
}

// ==== PROSES SIGN UP ====
if (isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $connection->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        echo "<script>
                window.onload = () => Swal.fire({
                    icon: 'error',
                    title: 'Email sudah terdaftar!',
                    text: 'Gunakan email lain untuk mendaftar.'
                });
              </script>";
    } else {
        $insert = $connection->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $insert->bind_param("sss", $username, $email, $password);
        if ($insert->execute()) {
            echo "<script>
                    window.onload = () => Swal.fire({
                        icon: 'success',
                        title: 'Akun berhasil dibuat!',
                        text: 'Silakan login untuk melanjutkan.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        document.querySelector('#authContainer').classList.remove('right-panel-active');
                    });
                  </script>";
        } else {
            echo "<script>
                    window.onload = () => Swal.fire({
                        icon: 'error',
                        title: 'Gagal mendaftar!',
                        text: 'Terjadi kesalahan sistem.'
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth - PS Billing</title>
    <link rel="stylesheet" href="auth.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="auth-container" id="authContainer">
        <!-- Panel Login -->
        <div class="form-container login-container" id="SIGNIN">
            <form method="POST" action="">
                <h2>Login</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Masuk</button>
                <p>Belum punya akun? <a href="#" class="switch">Sign Up</a></p>
            </form>
        </div>

        <!-- Panel Sign Up -->
        <div class="form-container signup-container" id="SIGNUP">
            <form method="POST" action="">
                <h2>Sign Up</h2>
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="signup">Daftar</button>
                <p>Sudah punya akun? <a href="#" class="switch">Login</a></p>
            </form>
        </div>

        <!-- Overlay Panel -->
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <a href="../../index.php" class="logo-link">
                        <img src="../assets/Logo.png" alt="Logo" class="logo" />
                    </a>
                    <h3>Untuk tetap terhubung, silakan masuk ke akun Anda</h3>
                    <button class="ghost" id="signIn">Login</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <a href="../../index.php" class="logo-link">
                        <img src="../assets/Logo.png" alt="Logo" class="logo" />
                    </a>
                    <h3>Daftarkan akun Anda untuk mulai menggunakan layanan PS Billing</h3>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="auth.js"></script>
</body>
</html>
