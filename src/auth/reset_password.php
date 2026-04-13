<?php

$errors = [];
$success = "";
$tokenValid = false;

// Get token from URL
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($token) {

    // Check token in DB
    $stmt = $db->prepare("
        SELECT id, reset_expires 
        FROM users 
        WHERE reset_token = ?
    ");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // Check expiry
        if (strtotime($user['reset_expires']) > time()) {
            $tokenValid = true;
            $userId = $user['id'];
        } else {
            $errors[] = "Token has expired.";
        }

    } else {
        $errors[] = "Invalid token.";
    }

    $stmt->close();
} else {
    $errors[] = "No token provided.";
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] === "POST" && $tokenValid) {

    $password = trim($_POST["password"] ?? "");
    $confirm  = trim($_POST["confirm_password"] ?? "");

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if ($confirm === "") {
        $errors[] = "Please confirm your password.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Update password + clear token
        $update = $db->prepare("
            UPDATE users 
            SET pwdHash = ?, reset_token = NULL, reset_expires = NULL
            WHERE id = ?
        ");
        $update->bind_param("si", $passwordHash, $userId);
        $update->execute();
        $update->close();

        $success = "Password successfully reset. You can now log in.";

        $tokenValid = false; // hide form after success
    }
}