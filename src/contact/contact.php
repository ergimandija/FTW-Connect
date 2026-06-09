<?php
$errors  = [];
$success = "";
$name    = "";
$email   = "";
$subject = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"]    ?? "");
    $email   = trim($_POST["email"]   ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name === "") {
        $errors[] = "Name is required.";
    }
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if ($subject === "") {
        $errors[] = "Subject is required.";
    }
    if ($message === "") {
        $errors[] = "Message is required.";
    }

    if (empty($errors)) {
        try {
            $stmt = $con->prepare("
                INSERT INTO contact_message (name, email, subject, message)
                VALUES (:name, :email, :subject, :message)
            ");
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':subject' => $subject,
                ':message' => $message,
            ]);

            $success = "Your message has been sent. We'll get back to you shortly.";
            $name = $email = $subject = $message = "";
        } catch (PDOException $e) {
            $errors[] = "Failed to send message. Please try again later.";
        }
    }
}
