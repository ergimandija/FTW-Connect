<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Definition der maximalen Inaktivitätszeit (30 Minuten = 1800 Sekunden)
$inaktivitaets_limit = 1800; 

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inaktivitaets_limit)) {
    session_unset();
    session_destroy();
    
    header("Location: login.php");
    exit();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['uid'])) {
    // $_SERVER['REQUEST_URI'] enthält den Pfad der aktuellen Seite
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    
    header("Location: login.php");
    exit();
}
?>