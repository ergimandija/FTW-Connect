<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../PHPMailer/src/Exception.php";
require __DIR__ . "/../PHPMailer/src/PHPMailer.php";
require __DIR__ . "/../PHPMailer/src/SMTP.php";

//require __DIR__ . "/../vendor/autoload.php";

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
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

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

            $resetLink = "http://localhost:3000/public/reset_password.php?token=" . urlencode($token);

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = $phpmail;
                $mail->Password = $phpmailPassword;
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom($mail, "FTW Connect");
                $mail->addAddress($email);

                $mail->isHTML(true);
                $mail->Subject = "Reset your password";
                $mail->Body = "
                    <p>Hello,</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href='$resetLink'>$resetLink</a></p>
                    <p>This link expires in 1 hour.</p>
                ";

                $mail->AltBody = "Reset your password using this link: $resetLink";

                $mail->send();

            } catch (Exception $e) {
                // Optional for development only:
                // error_log("Mailer Error: " . $mail->ErrorInfo);
            }
        }

        $success = "If this email exists, a reset link has been sent.";
    }
}
?>