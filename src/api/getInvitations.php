<?php
require '../../config/config.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$stmt = $con->prepare("
    SELECT
        i.id,
        i.chat_id,
        c.name AS chat_name,
        COALESCE(c.picture, './assets/img/anonymous.png') AS chat_picture,
        u.name AS invited_by_name,
        i.created_at
    FROM invitation i
    JOIN chat c ON i.chat_id = c.id
    JOIN users u ON i.invited_by = u.id
    WHERE i.invited_user = :uid AND i.status = 'pending'
    ORDER BY i.created_at DESC
");

$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

$invitations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($invitations);
