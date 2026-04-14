<?php
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

        try {
            $sql = "INSERT INTO users (name, email, pwdHash) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            
            $stmt->execute([$name, $email, $passwordHash]);
            
            $success = "Your account has been created successfully!";
            
            $name = $email = ""; 

        } catch (PDOException $e) {
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