<?php
header('Content-Type: application/json');
include '../../config/config.php';

if (empty($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

$data  = json_decode(file_get_contents('php://input'), true);
$mid   = intval($data['mid'] ?? 0);
$emoji = $data['emoji'] ?? '';

$allowed = ['👍', '❤️', '😂', '😮', '😢', '😡'];

if (!$mid || !in_array($emoji, $allowed)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$stmt = $con->prepare("
    SELECT cu.chat_id FROM chat_user cu
    JOIN message m ON m.chat_id = cu.chat_id
    WHERE m.id = :mid AND cu.user_id = :uid
");
$stmt->bindParam(':mid', $mid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
if (!$stmt->fetch()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not authorized']);
    exit;
}

try {
    $stmt = $con->prepare("SELECT 1 FROM message_reaction WHERE message_id = :mid AND user_id = :uid AND emoji = :emoji");
    $stmt->bindParam(':mid', $mid);
    $stmt->bindParam(':uid', $_SESSION['uid']);
    $stmt->bindParam(':emoji', $emoji);
    $stmt->execute();

    if ($stmt->fetch()) {
        $stmt = $con->prepare("DELETE FROM message_reaction WHERE message_id = :mid AND user_id = :uid AND emoji = :emoji");
    } else {
        $stmt = $con->prepare("INSERT INTO message_reaction (message_id, user_id, emoji) VALUES (:mid, :uid, :emoji)");
    }
    $stmt->bindParam(':mid', $mid);
    $stmt->bindParam(':uid', $_SESSION['uid']);
    $stmt->bindParam(':emoji', $emoji);
    $stmt->execute();

    echo json_encode(['status' => 'ok']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
