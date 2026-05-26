<?php
session_start();

require_once '../../config/config.php';

if (!isset($_SESSION['uid'])) {
    header("Location: ../../public/login.php");
    exit;
}

$userId = $_SESSION['uid'];

try {

    $stmt = $con->prepare("SELECT profilePicturePath FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user && !empty($user['profilePicturePath'])) {

        $path = "../../public/" . $user['profilePicturePath'];

        if (file_exists($path) && !str_contains($path, 'anonymous.png')) {
            unlink($path);
        }
    }

    $deletePosts = $con->prepare("DELETE FROM post WHERE user_id = ?");
    $deletePosts->execute([$userId]);

    $deleteUser = $con->prepare("DELETE FROM users WHERE id = ?");
    $deleteUser->execute([$userId]);

    session_unset();
    session_destroy();

    header("Location: ../../public/login.php?deleted=1");
    exit;

} catch (PDOException $e) {

    header("Location: ../../public/profile.php?error=" . urlencode($e->getMessage()));
    exit;
}