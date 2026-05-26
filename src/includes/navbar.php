<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['uid']);
?>

<header class="app-header">
  <div class="nav-container">

    <div class="logo">
      <img src="./assets/img/logo.png" alt="Logo" class="logo-img">
    </div>

    <input type="checkbox" id="burger-toggle" class="burger-toggle">

    <label for="burger-toggle" class="burger-menu">
      <span></span>
      <span></span>
      <span></span>
    </label>

    <nav class="nav-menu">
      <a href="./index.php" class="nav-item">Home</a>

      <?php if ($isLoggedIn): ?>
        <a href="./feed.php" class="nav-item">Feed</a>
        <a href="./userchats.php" class="nav-item">Messages</a>
        <a href="./profile.php" class="nav-item">Profile</a>
        <a href="./logout.php" class="nav-item">Logout</a>
      <?php else: ?>
        <a href="./login.php" class="nav-item">Login</a>
        <a href="./register.php" class="nav-item">Register</a>
      <?php endif; ?>
    </nav>

  </div>
</header>