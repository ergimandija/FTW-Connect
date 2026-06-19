<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/../PHPMailer/src/Exception.php";
require __DIR__ . "/../PHPMailer/src/PHPMailer.php";
require __DIR__ . "/../PHPMailer/src/SMTP.php";

$name = "";
$email = "";
$password = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name             = trim($_POST["name"] ?? "");
    $email            = trim($_POST["email"] ?? "");
    $password         = trim($_POST["password"] ?? "");
    $confirm_password = trim($_POST["confirm_password"] ?? "");

    if ($name === "") {
        $errors[] = "Name is required.";
    }

    if ($email === "") {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($password === "") {
        $errors[] = "Password is required.";
    }

    if ($confirm_password === "") {
        $errors[] = "Please confirm your password.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $verificationToken = bin2hex(random_bytes(32));
        $verificationTokenHash = hash("sha256", $verificationToken);
        $verificationExpires = date("Y-m-d H:i:s", time() + 86400); // 24 hours

        try {
            $con->beginTransaction();

            $sql = "
                INSERT INTO users 
                (name, email, pwdHash, status) 
                VALUES (?, ?, ?, 'pending')
            ";

            $stmt = $con->prepare($sql);
            $stmt->execute([
                $name,
                $email,
                $passwordHash
            ]);

            $userId = $con->lastInsertId();

            $verificationSql = "
                INSERT INTO email_verifications
                (user_id, token_hash, expires_at)
                VALUES (?, ?, ?)
            ";

            $verificationStmt = $con->prepare($verificationSql);
            $verificationStmt->execute([
                $userId,
                $verificationTokenHash,
                $verificationExpires
            ]);

            $con->commit();

            $verifyLink = "http://localhost:3000/public/verify_email.php?token=" . urlencode($verificationToken);

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;

                $mail->Username = $phpmail;

                $mail->Password = $phpmailPassword;

                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom($phpmail, "FTW Connect");
                $mail->addAddress($email, $name);

                $mail->isHTML(true);
                $mail->Subject = "Verify your FTW Connect account";

                $safeName = htmlspecialchars($name, ENT_QUOTES, "UTF-8");
                $safeVerifyLink = htmlspecialchars($verifyLink, ENT_QUOTES, "UTF-8");

                $mail->Body = "
                    <p>Hello {$safeName},</p>
                    <p>Thank you for registering at FTW Connect.</p>
                    <p>Please verify your email address by clicking the link below:</p>
                    <p><a href='{$safeVerifyLink}'>{$safeVerifyLink}</a></p>
                    <p>This link expires in 24 hours.</p>
                ";

                $mail->AltBody = "Hello {$name},\n\n"
                    . "Thank you for registering at FTW Connect.\n\n"
                    . "Please verify your email address using this link:\n"
                    . $verifyLink . "\n\n"
                    . "This link expires in 24 hours.";

                $mail->send();

                $success = "Your account has been created. Please check your email to activate your account.";

                $name = "";
                $email = "";

            } catch (Exception $e) {
                error_log("Mailer Error: " . $mail->ErrorInfo);
                $errors[] = "Account created, but verification email could not be sent.";
            }

        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }

            if ($e->getCode() == 23000) {
                $errors[] = "This email is already registered.";
            } else {
                error_log($e->getMessage());
                $errors[] = "A database error occurred. Please try again later.";
            }
        }
    }
}
?>