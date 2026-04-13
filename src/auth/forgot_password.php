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

        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();
            $userId = $user["id"];

            $token = bin2hex(random_bytes(32));

            $expires = date("Y-m-d H:i:s", time() + 3600);

            $updateStmt = $db->prepare("
                UPDATE users 
                SET reset_token = ?, reset_expires = ? 
                WHERE id = ?
            ");

            $updateStmt->bind_param("ssi", $token, $expires, $userId);
            $updateStmt->execute();
            $updateStmt->close();

            $resetLink = "http://localhost:3000/public/reset_password.php?token=" . $token;

            $success = "Reset link generated: $resetLink";

            //  Later: replace this with email sending

        } else {
            $success = "If this email exists, a reset link has been sent.";
        }

        $stmt->close();
    }
}