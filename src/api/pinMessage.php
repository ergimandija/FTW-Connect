<?php
header('Content-Type: application/json');
include '../../config/config.php';

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cid  = intval($data['cid'] ?? 0);
$mid  = intval($data['mid'] ?? 0);

if (!$cid) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
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
    if ($mid > 0) {
        $stmt = $con->prepare("SELECT id FROM message WHERE id = :mid AND chat_id = :cid");
        $stmt->bindParam(':mid', $mid);
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        if (!$stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => 'Message not found']);
            exit;
        }
    }

    $pinValue = $mid > 0 ? $mid : null;
    $stmt = $con->prepare("UPDATE chat SET pinned_message_id = :mid WHERE id = :cid");
    $stmt->bindParam(':mid', $pinValue, $pinValue === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
    $stmt->bindParam(':cid', $cid, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
