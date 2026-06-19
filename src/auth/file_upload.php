<?php
require_once __DIR__ . "/../utils/filevalidator.php";

$errors  = [];
$success = "";
$chatId  = (int)($_GET["cid"] ?? $_POST["cid"] ?? 0);

if ($chatId === 0) {
    $errors[] = "Invalid group.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && empty($errors)) {

    if (!isset($_FILES["file"]) || $_FILES["file"]["error"] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "No file selected.";
    } else {

        $memberCheck = $con->prepare("SELECT chat_id FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
        $memberCheck->bindParam(":cid", $chatId);
        $memberCheck->bindParam(":uid", $_SESSION["uid"]);
        $memberCheck->execute();
        $isMember = $memberCheck->rowCount() === 1;

        if (!$isMember) {
            $errors[] = "You are not a member of this group.";
        } else {

            $validator = new FileValidator();

            if (!$validator->validate($_FILES["file"])) {
                $errors = $validator->getErrors();
            } else {

                $file        = $_FILES["file"];
                $fileExt     = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
                $safeBase    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file["name"], PATHINFO_FILENAME));
                $newFileName = uniqid($safeBase . "_", true) . "." . $fileExt;

                $uploadDir = __DIR__ . "/../../public/assets/uploads/" . $chatId . "/";
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                if (move_uploaded_file($file["tmp_name"], $uploadDir . $newFileName)) {
                    $success = "File uploaded successfully.";
                } else {
                    $errors[] = "Failed to save the file.";
                }
            }
        }
    }
}