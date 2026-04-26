<?php

$errors = [];
$success = "";
$tokenValid = false;

$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($token) {
    try {
        $stmt = $con->prepare("
            SELECT id, reset_expires 
            FROM users 
            WHERE reset_token = ?
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch();

        if ($user) {
            if (strtotime($user['reset_expires']) > time()) {
                $tokenValid = true;
                $userId = $user['id'];
            } else {
                $errors[] = "Token has expired.";
            }
        } else {
            $errors[] = "Invalid token.";
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = "A database error occurred.";
    }
} else {
    $errors[] = "No token provided.";
}

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
        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $update = $con->prepare("
                UPDATE users 
                SET pwdHash = ?, reset_token = NULL, reset_expires = NULL
                WHERE id = ?
            ");
            
            $update->execute([$passwordHash, $userId]);

            $success = "Password successfully reset. You can now log in.";
            $tokenValid = false; 

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = "Could not update password. Please try again.";
        }
    }
}
?>