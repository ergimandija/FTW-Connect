<?php
require '../../config/config.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$chat_id = intval($data['chat_id'] ?? 0);
$user_id = intval($data['user_id'] ?? 0);

if ($chat_id <= 0 || $user_id <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $chat_id);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
$adminCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adminCheck || $adminCheck['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Not an admin']);
    exit;
}

$stmt = $con->prepare("SELECT id FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$stmt = $con->prepare("SELECT user_id FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $chat_id);
$stmt->bindParam(':uid', $user_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'User already in group']);
    exit;
}

$stmt = $con->prepare("SELECT id FROM invitation WHERE chat_id = :cid AND invited_user = :uid AND status = 'pending'");
$stmt->bindParam(':cid', $chat_id);
$stmt->bindParam(':uid', $user_id);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Invite already sent']);
    exit;
}

try {
    $stmt = $con->prepare("
        INSERT INTO invitation (chat_id, invited_by, invited_user)
        VALUES (:chat_id, :invited_by, :invited_user)
    ");

    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->bindParam(':invited_by', $_SESSION['uid']);
    $stmt->bindParam(':invited_user', $user_id);
    $stmt->execute();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}
