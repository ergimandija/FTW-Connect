<?php

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../src/auth/auth_check.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../public/profile.php");
    exit;
}

$userId = $_SESSION["uid"];

$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");

$imagePath = null;

if ($title === "") {
    header("Location: ../../public/profile.php?error=Post title is required");
    exit;
}

if ($content === "" && empty($_FILES["post_image"]["tmp_name"])) {
    header("Location: ../../public/profile.php?error=Post content or image is required");
    exit;
}

if (!empty($_FILES["post_image"]["tmp_name"]) && is_uploaded_file($_FILES["post_image"]["tmp_name"])) {

    $uploadDir = __DIR__ . "/../assets/uploads/posts/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileExtension = strtolower(pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION));
    $fileName = uniqid("post_", true) . "." . $fileExtension;
    $tmpName = $_FILES["post_image"]["tmp_name"];
    $fileSize = $_FILES["post_image"]["size"];
    $fileMime = mime_content_type($tmpName);

    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
    $allowedMimeTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    $maxFileSize = 5 * 1024 * 1024;

    if (
        $_FILES["post_image"]["error"] === UPLOAD_ERR_OK &&
        $fileSize > 0 &&
        $fileSize <= $maxFileSize &&
        in_array($fileExtension, $allowedExtensions, true) &&
        in_array($fileMime, $allowedMimeTypes, true)
    ) {
        $imagePath = "assets/uploads/posts/" . $fileName;

        if (!move_uploaded_file($tmpName, $uploadDir . $fileName)) {
            header("Location: ../../public/profile.php?error=Image upload failed");
            exit;
        }
    } else {
        header("Location: ../../public/profile.php?error=Invalid image file");
        exit;
    }
}

try {
    $stmt = $con->prepare("
        INSERT INTO post 
        (title, content, image_url, user_id)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $title,
        $content,
        $imagePath,
        $userId
    ]);

    header("Location: ../../public/profile.php");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    header("Location: ../../public/profile.php?error=Could not create post");
    exit;
}