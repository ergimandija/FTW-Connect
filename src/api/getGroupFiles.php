<?php
require '../../config/config.php';

header('Content-Type: application/json');

$chatId = (int)($_GET['cid'] ?? 0);

if (!$chatId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing chat ID']);
    exit;
}

$stmt = $con->prepare("SELECT chat_id FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $chatId);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not a member of this group']);
    exit;
}

$uploadDir = __DIR__ . '/../../public/assets/uploads/' . $chatId;

if (!is_dir($uploadDir)) {
    echo json_encode(['status' => 'success', 'files' => []]);
    exit;
}

$files = [];
$items = scandir($uploadDir);

if ($items === false) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to read files']);
    exit;
}

foreach ($items as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }

    $filePath = $uploadDir . '/' . $item;

    if (!is_file($filePath)) {
        continue;
    }

    $fileExt = strtolower(pathinfo($item, PATHINFO_EXTENSION));
    $fileSize = filesize($filePath);
    $fileModified = filemtime($filePath);

    $files[] = [
        'name' => $item,
        'url' => './assets/uploads/' . $chatId . '/' . $item,
        'ext' => $fileExt,
        'size' => $fileSize,
        'modified' => $fileModified
    ];
}

usort($files, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

echo json_encode(['status' => 'success', 'files' => $files]);
