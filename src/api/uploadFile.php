<?php
require '../../config/config.php';
require_once __DIR__ . '/../utils/filevalidator.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$cid = intval($_POST['cid'] ?? 0);

if ($cid <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid group.']);
    exit;
}

$stmt = $con->prepare("SELECT chat_id FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'You are not a member of this group.']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'No file selected.']);
    exit;
}

$validator = new FileValidator();

if (!$validator->validate($_FILES['file'])) {
    http_response_code(422);
    echo json_encode(['status' => 'error', 'message' => implode(' ', $validator->getErrors())]);
    exit;
}

$file        = $_FILES['file'];
$fileExt     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$safeBase    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
$newFileName = uniqid($safeBase . '_', true) . '.' . $fileExt;

$uploadDir = __DIR__ . '/../../public/assets/uploads/' . $cid . '/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to save the file.']);
    exit;
}

echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.']);
