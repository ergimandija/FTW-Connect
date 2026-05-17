<?php 
include '../../config/config.php';
?> 
<?php
session_start();

require_once __DIR__ . '/../utils/filevalidator.php';

if (!isset($_SESSION['uid'])) {
    header("Location: ../../login.php");
    exit;
}

$userId = $_SESSION['uid'];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($name)) {
        $errors[] = "Name cannot be empty.";
    }

    if (empty($email)) {
        $errors[] = "Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }

    $dbImagePath = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $validator = new FileValidator();
        
        if (!$validator->validate($_FILES['profile_pic'])) {
            $errors = array_merge($errors, $validator->getErrors());
        } else {
            $file = $_FILES['profile_pic'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = "user_" . $userId . "_" . time() . "." . $ext;
            
            $uploadDir = __DIR__ . "/../../public/assets/uploads/profiles/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                $dbImagePath = "assets/uploads/profiles/" . $fileName;
            } else {
                $errors[] = "Error saving the image.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $sql = "UPDATE users SET name = ?, email = ?";
            $params = [$name, $email];

            if (!empty($password)) {
                $sql .= ", pwdHash = ?";
                $params[] = password_hash($password, PASSWORD_DEFAULT);
            }

            if ($dbImagePath) {
                $sql .= ", profilePicturePath = ?";
                $params[] = $dbImagePath;
            }

            $sql .= " WHERE id = ?";
            $params[] = $userId;

            $stmt = $con->prepare($sql);
            $stmt->execute($params);

            header("Location: ../../public/profile.php?success=1");
            exit;

        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

if (!empty($errors)) {
    header("Location: ../../public/profile.php?error=" . urlencode(implode(", ", $errors)));
    exit;
}