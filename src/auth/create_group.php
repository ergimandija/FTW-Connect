<?php
$groupName = "";
$groupDescription = "";
$errors = [];
$success = "";
$createdChatId = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $groupName        = trim($_POST["name"] ?? "");
    $groupDescription = trim($_POST["description"] ?? "");

    if ($groupName === "") {
        $errors[] = "Group name is required.";
    } elseif (strlen($groupName) > 255) {
        $errors[] = "Group name must not exceed 255 characters.";
    }

    $picturePath = null;

    if (!empty($_FILES["picture"]["name"])) {

        $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
        $allowedMimeTypes  = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        $maxSize           = 2 * 1024 * 1024;

        $fileName = $_FILES["picture"]["name"];
        $fileTmp = $_FILES["picture"]["tmp_name"];
        $fileSize = $_FILES["picture"]["size"];
        $fileMime = mime_content_type($fileTmp);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            $errors[] = "Invalid picture extension. Allowed: jpg, jpeg, png, gif, webp.";
        } elseif (!in_array($fileMime, $allowedMimeTypes)) {
            $errors[] = "Invalid picture type.";
        } elseif ($fileSize > $maxSize) {
            $errors[] = "Picture must not exceed 2MB.";
        } else {
            $uploadDir = __DIR__ . "/../../public/assets/img/groups/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $newFileName = uniqid("grp_", true) . "." . $fileExt;
            if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
                $picturePath = "assets/img/groups/" . $newFileName;
            } else {
                $errors[] = "Failed to save picture.";
            }
        }
    }

    if (empty($errors)) {

        $stmt = $db->prepare("INSERT INTO chat (name, description, picture) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $groupName, $groupDescription, $picturePath);

        try {
            $stmt->execute();
            $createdChatId = $db->insert_id;

            $memberStmt = $db->prepare("INSERT INTO chat_user (chat_id, user_id, role, type) VALUES (?, ?, ?, ?)");
            $role = "admin";
            $type = "group";
            $memberStmt->bind_param("iiss", $createdChatId, $_SESSION["uid"], $role, $type);
            $memberStmt->execute();
            $memberStmt->close();

            $success      = "Group created successfully!";
            $groupName    = "";
            $groupDescription = "";

        } catch (mysqli_sql_exception $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }

        $stmt->close();
    }
}