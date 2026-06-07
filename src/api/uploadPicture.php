<?php

$uploadDir = "../../public/assets/img/chats/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!isset($_FILES["picture"])) {
    http_response_code(400);
    echo "No file uploaded";
    exit;
}

$file = $_FILES["picture"];
$originalName = basename($file["name"]);

$targetPath = $uploadDir . $originalName;

if (move_uploaded_file($file["tmp_name"], $targetPath)) {
    echo json_encode([
        "success" => true,
        "filename" =>  $originalName
    ]);
} else {
    http_response_code(500);
    echo "Upload failed";
}


?>