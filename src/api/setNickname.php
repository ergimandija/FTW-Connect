<?php
header('Content-Type: application/json');
include '../../config/config.php';

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data     = json_decode(file_get_contents('php://input'), true);
$cid      = intval($data['cid'] ?? 0);
$targetUid = intval($data['uid'] ?? 0);
$nickname = trim($data['nickname'] ?? '');

if (!$cid || !$targetUid) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if (mb_strlen($nickname) > 255) {
    echo json_encode(['status' => 'error', 'message' => 'Nickname too long']);
    exit;
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not a member']);
    exit;
}

$isSelf  = ($targetUid === intval($_SESSION['uid']));
$isAdmin = ($row['role'] === 'admin');

if (!$isSelf && !$isAdmin) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

$nicknameValue = $nickname !== '' ? $nickname : null;

$stmt = $con->prepare("UPDATE chat_user SET nickname = :nickname WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':nickname', $nicknameValue);
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $targetUid);
$stmt->execute();

echo json_encode(['status' => 'ok']);
