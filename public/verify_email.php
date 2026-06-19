<?php

require_once __DIR__ . '/../config/config.php';

$message = "";

$token = $_GET["token"] ?? "";

if ($token === "") {
    $message = "Invalid verification link.";
} else {
    $tokenHash = hash("sha256", $token);

    try {
        $stmt = $con->prepare("
            SELECT 
                ev.id AS verification_id,
                ev.user_id,
                ev.expires_at,
                u.status
            FROM email_verifications ev
            INNER JOIN users u ON ev.user_id = u.id
            WHERE ev.token_hash = ?
            LIMIT 1
        ");

        $stmt->execute([$tokenHash]);
        $verification = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$verification) {
            $message = "Invalid verification link.";
        } elseif ($verification["status"] === "active") {
            $message = "Your email is already verified. You can log in.";
        } elseif (strtotime($verification["expires_at"]) < time()) {
            $deleteStmt = $con->prepare("
                DELETE FROM email_verifications
                WHERE id = ?
            ");
            $deleteStmt->execute([$verification["verification_id"]]);

            $message = "This verification link has expired.";
        } else {
            $con->beginTransaction();

            $updateStmt = $con->prepare("
                UPDATE users 
                SET status = 'active'
                WHERE id = ?
            ");

            $updateStmt->execute([$verification["user_id"]]);

            $deleteStmt = $con->prepare("
                DELETE FROM email_verifications
                WHERE id = ?
            ");

            $deleteStmt->execute([$verification["verification_id"]]);

            $con->commit();

            $message = "Your email has been verified. You can now log in.";
        }

    } catch (PDOException $e) {
        if ($con->inTransaction()) {
            $con->rollBack();
        }

        error_log($e->getMessage());
        $message = "A database error occurred. Please try again later.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification | FTW Connect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex align-items-center justify-content-center vh-100">

<div class="text-center" style="max-width: 420px;">
    <h3>Email Verification</h3>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="login.php" class="btn btn-primary">Go to Login</a>
</div>

</body>
</html>