<?php


$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    try {
        $sql = "SELECT id, name, email, pwdHash, failed_attempts, locked_until FROM users WHERE email = ?";
        $stmt = $con->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user) {
            $uid = $user["id"];
            $passwordHash = $user["pwdHash"];
            $failedAttempts = $user["failed_attempts"];
            $lockedUntil = $user["locked_until"];

            
            if ($lockedUntil && strtotime($lockedUntil) > time()) {
                $errors[] = "Account is locked. Try again later.";
            } 
            else {
                if (password_verify($password, $passwordHash)) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["uid"] = $uid;
                    $_SESSION["name"] = $user["name"];
                    $_SESSION["email"] = $user["email"];

                    $updateStmt = $con->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                    $updateStmt->execute([$uid]);

                    header("Location: index.php");
                    exit();
                } 
                else {
                    $failedAttempts++;
                    
                    if ($failedAttempts % 5 == 0) {
                        $lockTime = date("Y-m-d H:i:s", time() + 900); 
                        $lockStmt = $con->prepare("UPDATE users SET failed_attempts = ?, locked_until = ? WHERE id = ?");
                        $lockStmt->execute([$failedAttempts, $lockTime, $uid]);
                        $errors[] = "Account locked due to multiple failed attempts. Try again in 15 minutes.";
                    } else {
                        $updateStmt = $con->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
                        $updateStmt->execute([$failedAttempts, $uid]);
                        $errors[] = "Invalid email or password.";
                    }
                }
            }
        } else {
            $errors[] = "Invalid email or password.";
        }

    } catch (PDOException $e) {
        error_log($e->getMessage());
        $errors[] = "A database error occurred.";
    }
    $con = null;
}
?>