<?php
$errors = [];
$success = "";
$groupName = "";
$groupDescription = "";
$groupPicture = null;
$accessDenied = false;

if (empty($_SESSION['uid'])) {
    header('Location: ../public/index.php');
    exit;
}

$cid = intval($_GET['cid'] ?? $_POST['cid'] ?? 0);

if ($cid <= 0) {
    die('Invalid group ID');
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
$memberCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$memberCheck || $memberCheck['role'] !== 'admin') {
    $accessDenied = true;
} else {
    $stmt = $con->prepare("SELECT name, description, picture FROM chat WHERE id = :id");
    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$group) {
        die('Group not found');
    }

    $groupName        = $group['name'];
    $groupDescription = $group['description'] ?? '';
    $groupPicture     = $group['picture'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $groupName        = trim($_POST["name"] ?? "");
        $groupDescription = trim($_POST["description"] ?? "");

        if ($groupName === "") {
            $errors[] = "Group name is required.";
        } elseif (strlen($groupName) > 255) {
            $errors[] = "Group name must not exceed 255 characters.";
        }

        $newPicturePath = $groupPicture;

        if (empty($errors) && !empty($_FILES["picture"]["name"])) {
            $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $allowedMimeTypes  = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            $maxSize           = 2 * 1024 * 1024;

            $fileName = $_FILES["picture"]["name"];
            $fileTmp  = $_FILES["picture"]["tmp_name"];
            $fileSize = $_FILES["picture"]["size"];
            $fileMime = mime_content_type($fileTmp);
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExtensions)) {
                $errors[] = "Invalid picture extension.";
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
                    if ($groupPicture) {
                        $oldPath = __DIR__ . "/../../public/" . $groupPicture;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $newPicturePath = "assets/img/groups/" . $newFileName;
                } else {
                    $errors[] = "Failed to save picture.";
                }
            }
        }

        if (empty($errors)) {
            try {
                $stmt = $con->prepare("UPDATE chat SET name = :name, description = :description, picture = :picture WHERE id = :id");
                $stmt->execute([
                    ':name'        => $groupName,
                    ':description' => $groupDescription,
                    ':picture'     => $newPicturePath,
                    ':id'          => $cid,
                ]);

                $success      = "Group updated successfully!";
                $groupPicture = $newPicturePath;
            } catch (PDOException $e) {
                $errors[] = "Database error: " . $e->getMessage();
            }
        }
    }
}
