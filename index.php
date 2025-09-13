<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Extension - Booking Playstation</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- NAVBAR -->
  <header class="navbar">
    <div class="logo">
        < href="#hero">
            <img src="./assets/Logo.png">
        
    </div>
    <nav>
      <ul>
        <li><a href="#hero">Beranda</a></li>
        <li><a href="#available">Unit Tersedia</a></li>
        <li><a href="#pricing">Paket harga</a></li>
        <li><a href="../Unit/unit.php">Pesanan</a></li>
      </ul>
    </nav>
    <div class="auth">
      <a href="../Login/login.php" class="login">Login</a>
      <a href="../Sign-Up/signup.php" class="signup">Sign Up</a>
    </div>
  </header>

  <!-- HERO -->
  <section class="hero" id="hero">
    <div class="hero-content">
      <h1>Layanan Billing PlayStation Terpercaya</h1>
      <p>Nikmati pengalaman gaming terbaik dengan sistem billing yang mudah dan transparan. 
      Berbagai paket harga sesuai kebutuhan Anda.</p>
      <a href="#available" class="btn-primary">Pesan Sekarang</a>
    </div>
  </section>


  <!-- AVAILABLE -->
  <section class="available" id="available">
    <h2>Unit Tersedia</h2>
    <h3>Cek ketersediaan console PlayStation dan perangkat gaming lainnya</h3>
    <div class="card-container">
      <div class="card unit">
        <h3>PlayStation 4</h3>
        <span class="status available">Tersedia</span>
        <p><strong>10 Unit</strong></p>
        <p>Console gaming generasi ke-8</p>
        <a href="#" class="btn-dark reservasi">Reservasi</a>
      </div>


      
      <div class="card unit">
        <h3>PlayStation 5</h3>
        <span class="status available">Tersedia</span>
        <p><strong>15 Unit</strong></p>
        <p>Console gaming terbaru dengan teknologi canggih</p>
        <a href="#" class="btn-dark reservasi">Reservasi</a>
      </div>
      <div class="card unit">
        <h3>VR Station</h3>
        <span class="status available">Tersedia</span>
        <p><strong>5 Unit</strong></p>
        <p>Pengalaman gaming virtual reality</p>
        <a href="#" class="btn-dark reservasi">Reservasi</a>
      </div>
    </div>
  </section>


  <!-- PRICING -->
  <section class="pricing" id="pricing">
    <h2>Paket Harga</h2>
    <p>Pilih paket yang sesuai dengan kebutuhan gaming Anda</p>
    <div class="card-container">
      <div class="card">
        <h3>1 Jam</h3>
        <p class="price">Rp 15.000</p>
        <p>Cocok untuk gaming singkat</p>
        <a href="#" class="btn-secondary paket">Pilih Paket</a>
      </div>
      <div class="card">
        <h3>3 Jam</h3>
        <p class="price">Rp 40.000</p>
        <p>Paket populer untuk gaming santai</p>
        <a href="#" class="btn-secondary paket">Pilih Paket</a>
      </div>
      <div class="card">
        <h3>5 Jam</h3>
        <p class="price">Rp 65.000</p>
        <p>Hemat untuk gaming marathon</p>
        <a href="#" class="btn-secondary paket">Pilih Paket</a>
      </div>
      <div class="card">
        <h3>10 Jam</h3>
        <p class="price">Rp 120.000</p>
        <p>Paket terbaik untuk hardcore</p>
        <a href="#" class="btn-secondary paket">Pilih Paket</a>
      </div>
      <div class="card">
        <h3>15 Jam</h3>
        <p class="price">Rp 175.000</p>
        <p>Paket SPECIAL untuk hardcore</p>
        <a href="#" class="btn-secondary paket">Pilih Paket</a>
      </div>
    </div>
  </section>



  <!-- FOOTER -->
  <footer class="footer">
  <div class="footer-container">
    <div class="footer-col">
      <h3>PS Billing</h3>
      <p>Layanan billing PlayStation terpercaya dengan sistem yang mudah dan transparan.</p>
    </div>
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="#hero">Beranda</a></li>
        <li><a href="#available">Unit Tersedia</a></li>
        <li><a href="#pricing">Paket Harga</a></li>
        <li><a href="">Pesanan</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h4>Social Media</h4>
      <ul>
        <li><a href="#">Instagram</a></li>
        <li><a href="#">Facebook</a></li>
        <li><a href="#">Twitter</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2025 PS Billing. All rights reserved.</p>
  </div>
</footer>

  <!-- POPUP MODAL -->
  <div id="authModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Perhatian !!</h2>
      <p>Silakan login atau sign up terlebih dahulu sebelum memesan.</p>
      <div class="modal-actions">
        <a href="./Login/login.php" class="btn-secondary">Login</a></button>
        <a href="./Sign-Up/signup.php" class="btn-dark">Sign Up</a>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>
