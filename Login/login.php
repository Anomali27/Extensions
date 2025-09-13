<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login Extension</title>
  <link rel="stylesheet" href="stylelogin.css" />
  <script defer src="scriptlogin.js"></script>
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
  />
</head>
<body class="body">
  <div class="container">
    <!-- Left side -->
    <div class="left-panel">
      <h1 class="title">Welcome Back To <br />Extension</h1>
      <p class="subtitle">Login with Username &amp; Password</p>
      <button class="btn-outline">Login</button>
      <a href="../Sign-Up/signup.php"><button class="btn-primary">Sign Up</button></a>
      <a href="../index.php" class="back-link">← Back</a>
    </div>

    <!-- Right side -->
    <div class="right-panel">
      <!-- Logo klik balik ke index.php -->
      <a href="../index.php" class="logo-link">
        <img src="../assets/Logo.png" alt="Logo" class="logo-img">
      </a>

      <form class="form">
        <div class="form-group">
          <label for="username" class="label">Username</label>
          <input id="username" name="username" type="text" class="input" autocomplete="username"/>
        </div>
        <div class="form-group">
          <label for="password" class="label">Password</label>
          <input id="password" name="password" type="password" class="input" autocomplete="current-password"/>
        </div>
        <div class="form-action">
          <button type="submit" class="btn-login">Login</button>
        </div>
      </form>
      <p class="register-text">
        Don't have an account?
        <a href="../Sign-Up/signup.php" class="register-link">Sign Up</a>
      </p>
    </div>
  </div> 

</body>
</html>
