<?php
require '../../config/config.php';

header('Content-Type: application/json');

if (empty($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$q = trim($_GET['q'] ?? '');
$cid = intval($_GET['cid'] ?? 0);

if (empty($q) || $cid <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$stmt = $con->prepare("SELECT user_id FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Access denied']);
    exit;
}

$searchTerm = '%' . $q . '%';

$stmt = $con->prepare("
    SELECT u.id, u.name, u.email, u.profilePicturePath
    FROM users u
    WHERE (u.name LIKE :term OR u.email LIKE :term)
    AND u.id NOT IN (
        SELECT user_id FROM chat_user WHERE chat_id = :cid
        UNION
        SELECT invited_user FROM invitation WHERE chat_id = :cid AND status = 'pending'
    )
    AND u.id != :uid
    LIMIT 10
");

$stmt->bindParam(':term', $searchTerm);
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
