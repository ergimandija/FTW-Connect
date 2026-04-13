<?php
$name = "";
$email = "";
$password = "";
$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST["name"] ?? "");
    $email     = trim($_POST["email"] ?? "");
    $password  = trim($_POST["password"] ?? "");
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

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);


        $sql = "INSERT INTO users (name, email, pwdHash) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt === false) {
            die("Prepare failed: " . $db->error);
        }

        $stmt->bind_param("sss", $name, $email, $passwordHash);

        try {
            $stmt->execute();
            $success = "Your account has been created successfully!";
        } catch (mysqli_sql_exception $e) {

            if ($e->getCode() === 1062) {
                $errors[] = "This email is already registered.";
            } else {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
        $stmt->close();
        $db->close();
    }
}
?>
