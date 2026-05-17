<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['uid'])) {
    // $_SERVER['REQUEST_URI'] enthält den Pfad der aktuellen Seite (z.B. /profile.php)
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    
    header("Location: login.php");
    exit();
}