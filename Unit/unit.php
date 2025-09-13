<?php
// unit.php - Demo frontend (tidak butuh backend untuk demo)
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Unit Booking - PS Biling</title>
  <link rel="stylesheet" href="styleunit.css" />
</head>
<body>
  <div class="site-bg"> <!-- wrapper putih penuh -->
    <header class="topbar">
      <div class="top-left">
        <button class="back" aria-label="kembali">←</button>
        <img src="../assets/Logo.png" alt="logo" class="logo" />
      </div>
      <nav class="breadcrumb">Unit › <span>Confirm</span> › Payment</nav>
      <div class="legend">
        <div><span class="dot available"></span>Available</div>
        <div><span class="dot booked"></span>Booked</div>
      </div>
    </header>

    <main class="page-wrap">
      <section class="units">
        <!-- Kolom kiri (3 kolom button mirip A,B,C) -->
        <div class="unit-column">
          <img src="../assets/PS5.png" alt="PS5" class="device-logo" />
          <div class="seats seats-3cols">
            <button class="seat booked" data-seat="A1">A1</button>
            <button class="seat booked" data-seat="B1">B1</button>
            <button class="seat booked" data-seat="C1">C1</button>

            <button class="seat available" data-seat="A2">A2</button>
            <button class="seat available" data-seat="B2">B2</button>
            <button class="seat booked" data-seat="C2">C2</button>

            <button class="seat available" data-seat="A3">A3</button>
            <button class="seat available" data-seat="B3">B3</button>
            <button class="seat booked" data-seat="C3">C3</button>

            <button class="seat available" data-seat="A4">A4</button>
            <button class="seat booked" data-seat="B4">B4</button>
            <button class="seat booked" data-seat="C4">C4</button>

            <button class="seat available" data-seat="A5">A5</button>
            <button class="seat booked" data-seat="B5">B5</button>
            <button class="seat available" data-seat="C5">C5</button>
          </div>
        </div>

        <!-- Kolom tengah (2 kolom button E,F) -->
        <div class="unit-column center">
          <img src="../assets/PS4.png" alt="PS4" class="device-logo" />
          <div class="seats seats-2cols">
            <button class="seat booked" data-seat="D1">D1</button>
            <button class="seat booked" data-seat="E1">E1</button>

            <button class="seat available" data-seat="D2">D2</button>
            <button class="seat booked" data-seat="E2">E2</button>

            <button class="seat booked" data-seat="D3">D3</button>
            <button class="seat available" data-seat="E3">E3</button>

            <button class="seat available" data-seat="D4">D4</button>
            <button class="seat booked" data-seat="E4">E4</button>

            <button class="seat available" data-seat="D5">D5</button>
            <button class="seat booked" data-seat="E5">E5</button>
          </div>
        </div>

        <!-- Kolom kanan (1 kolom VR) -->
        <div class="unit-column right">
          <img src="../assets/VR.png" alt="VR" class="device-logo1" />
          <div class="seats seats-1col">
            <button class="seat booked" data-seat="F1">F1</button>
            <button class="seat booked" data-seat="F2">F2</button>
            <button class="seat booked" data-seat="F3">F3</button>
            <button class="seat booked" data-seat="F4">F4</button>
            <button class="seat available" data-seat="F5">F5</button>
          </div>
        </div>
      </section>
    </main>

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
  </div>

  <!-- Modal -->
  <div id="modal" class="modal" aria-hidden="true">
    <div class="modal-content">
      <button class="close" aria-label="tutup">×</button>
      <h2 id="modal-seat">Seat</h2>
      <p id="modal-status">Status</p>
      <div class="modal-actions">
        <button id="btn-reserve" class="btn">Reserve</button>
        <button id="btn-cancel" class="btn ghost">Cancel</button>
      </div>
    </div>
  </div>

  <script src="scriptunit.js"></script>
</body>
</html>
