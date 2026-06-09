<?php
$errors  = [];
$success = "";
$name    = "";
$email   = "";
$subject = "";
$message = "";

function sendContactEmail(string $name, string $email, string $subject, string $message): bool {
    $payload = json_encode([
        'from'    => RESEND_FROM,
        'to'      => [RESEND_TO],
        'reply_to'=> $email,
        'subject' => "[FTW Connect] $subject",
        'text'    => "New contact form submission.\n\n"
                   . "Name:    $name\n"
                   . "Email:   $email\n"
                   . "Subject: $subject\n\n"
                   . "Message:\n$message",
    ]);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $payload,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . RESEND_API_KEY,
        ],
    ]);

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    return $httpCode === 200 || $httpCode === 201;
}

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
        } catch (PDOException $e) {
            $errors[] = "Failed to save message. Please try again later.";
        }

        if (empty($errors)) {
            sendContactEmail($name, $email, $subject, $message);
            $success = "Your message has been sent. We'll get back to you shortly.";
            $name = $email = $subject = $message = "";
        }
    }
}
