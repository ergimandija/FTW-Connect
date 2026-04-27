<?php
require '../../config/config.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

$invitation_id = intval($data['invitation_id'] ?? 0);
$action = $data['action'] ?? '';

if ($invitation_id <= 0 || !in_array($action, ['accept', 'decline'], true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$stmt = $con->prepare("
    SELECT chat_id, status FROM invitation
    WHERE id = :id AND invited_user = :uid
");

$stmt->bindParam(':id', $invitation_id);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

$invitation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invitation) {
    http_response_code(404);
    echo json_encode(['status' => 'error', 'message' => 'Invitation not found']);
    exit;
}

if ($invitation['status'] !== 'pending') {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Invitation already responded']);
    exit;
}

try {
    $con->beginTransaction();

    if ($action === 'accept') {
        $stmt = $con->prepare("
            INSERT INTO chat_user (chat_id, user_id, role, type)
            VALUES (:chat_id, :user_id, NULL, 'group')
        ");

        $stmt->bindParam(':chat_id', $invitation['chat_id']);
        $stmt->bindParam(':user_id', $_SESSION['uid']);
        $stmt->execute();
    }

    $stmt = $con->prepare("
        UPDATE invitation
        SET status = :status
        WHERE id = :id
    ");

    $newStatus = $action === 'accept' ? 'accepted' : 'declined';
    $stmt->bindParam(':status', $newStatus);
    $stmt->bindParam(':id', $invitation_id);
    $stmt->execute();

    $con->commit();

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    $con->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error']);
    exit;
}
