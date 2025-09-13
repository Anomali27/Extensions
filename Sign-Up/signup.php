<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sign Up Page</title>
  <link rel="stylesheet" href="signup.css" />
  <script defer src="signup.js"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
</head>
<body class="body">
  <div class="signup-container">
    <!-- Form Section -->
    <div class="form-section">
    <!-- Logo klik balik ke homepage -->
    <a href="../index.php" class="logo-link">
      <img src="../assets/Logo.png" alt="Logo" class="logo-img">
    </a>

    <form class="signup-form">
      <div class="form-group">
        <label for="username" class="label">Username</label>
        <input id="username" type="text" class="input" />
      </div>
      <div class="form-group">
        <label for="email" class="label">Email</label>
        <input id="email" type="email" class="input" />
      </div>
      <div class="form-group">
        <label for="password" class="label">Password</label>
        <input id="password" type="password" class="input" />
      </div>
      <div class="form-action">
        <button type="submit" class="btn-submit">Sign Up</button>
      </div>
    </form>
    <p class="redirect-text">
      Already have an account?
      <a href="../Login/login.php" class="redirect-link">Login</a>
    </p>
  </div>


    <!-- Info Section -->
    <div class="info-section">
      <h2 class="info-title">Create Your Account</h2>
      <p class="info-subtitle">Sign up to get started</p>
      <a href="../Login/login.php">
        <button class="btn-light">Login</button>
      </a>
      <button class="btn-outline">Sign Up</button>
      <a href="../index.php" class="back-link">← Back</a>
    </div>
  </div>
</body>
</html>
