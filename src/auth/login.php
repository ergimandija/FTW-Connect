<?php
ini_set('session.gc_maxlifetime', 1200); 
session_start();

$errors = [];


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["email"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $sql = "SELECT id, name, email, pwdHash, failed_attempts, locked_until FROM users WHERE email = ?";
    $stmt = $db->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $db->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($uid, $name, $email, $passwordHash, $failedAttempts, $lockedUntil);
        $stmt->fetch();

        if ($lockedUntil && strtotime($lockedUntil) > time()) {
            $errors[] = "Account is locked. Try again later.";
        }
        else {
            if (password_verify($password, $passwordHash)) {

                $_SESSION["loggedin"] = true;
                $_SESSION["uid"] = $uid;
                $_SESSION["name"] = $name;
                $_SESSION["email"] = $email;
                $updateSql = "UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->bind_param("i", $uid);
                $updateStmt->execute();
                header("Location: index.php");
                exit();
            }
            else {
                $failedAttempts++;
                $updateSql = "UPDATE users SET failed_attempts = ? WHERE id = ?";
                $updateStmt = $db->prepare($updateSql);
                $updateStmt->bind_param("ii", $failedAttempts, $uid);
                $updateStmt->execute();
                

                if ($failedAttempts!=0 && $failedAttempts % 5 == 0) {
                    $lockUntil = date("Y-m-d H:i:s", time() + 900); 
                    $lockSql = "UPDATE users SET locked_until = ? WHERE id = ?";
                    $lockStmt = $db->prepare($lockSql);
                    $lockStmt->bind_param("si", $lockUntil, $uid);
                    $lockStmt->execute();
                    $errors[] = "Account locked due to multiple failed attempts. Try again in 15 minutes.";

                    $lockStmt->close();

                }
                
            }
        $updateStmt->close();
        }
        
    }

    $errors[] = "Invalid email or password.";

    $stmt->close();
    $db->close();
}
?>
