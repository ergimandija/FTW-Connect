<?php

$email = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($errors)) {

        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(); // Holt die nächste Zeile als Array

        if ($user) {
            $userId = $user["id"];

            $token = bin2hex(random_bytes(32));

            $expires = date("Y-m-d H:i:s", time() + 3600);

            $updateStmt = $con->prepare("
                UPDATE users 
                SET reset_token = ?, reset_expires = ? 
                WHERE id = ?
            ");

            $updateStmt->execute([$token, $expires, $userId]);
            $resetLink = "http://localhost:3000/public/reset_password.php?token=" . $token;

            $success = "Reset link generated: $resetLink";

            //  Later: replace this with email sending

        } else {
            $success = "If this email exists, a reset link has been sent.";
        }

    }
}
?>