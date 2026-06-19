<?php
header('Content-Type: application/json');
include '../../config/config.php';

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data      = json_decode(file_get_contents('php://input'), true);
$cid       = intval($data['cid'] ?? 0);
$targetUid = intval($data['uid'] ?? 0);
$action    = $data['action'] ?? '';

if (!$cid || !$targetUid || !in_array($action, ['kick', 'promote', 'demote'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

if ($targetUid === intval($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot target yourself']);
    exit;
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row || $row['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

try {
    if ($action === 'kick') {
        $stmt = $con->prepare("DELETE FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
    } elseif ($action === 'promote') {
        $stmt = $con->prepare("UPDATE chat_user SET role = 'admin' WHERE chat_id = :cid AND user_id = :uid");
    } elseif ($action === 'demote') {
        $stmt = $con->prepare("UPDATE chat_user SET role = 'member' WHERE chat_id = :cid AND user_id = :uid");
    }

    $stmt->bindParam(':cid', $cid);
    $stmt->bindParam(':uid', $targetUid);
    $stmt->execute();

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
