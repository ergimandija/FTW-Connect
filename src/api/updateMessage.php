<?php
header('Content-Type: application/json');

include '../../config/config.php';
include '../includes/crypto.php';

try {

    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data) {
        throw new Exception("Invalid JSON");
    }

    if (!isset($_SESSION['uid'])) {
        throw new Exception("No session");
    }

    if (empty($data['message_id']) || empty($data['message'])) {
        throw new Exception("Missing fields");
    }

    // Check if message belongs to logged in user
    $stmt = $con->prepare("
        SELECT * FROM message 
        WHERE id = :mid 
        AND sender_id = :uid
    ");

    $stmt->bindParam(":mid", $data['message_id']);
    $stmt->bindParam(":uid", $_SESSION['uid']);
    $stmt->execute();

    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        throw new Exception("Unauthorized");
    }

    // Encrypt updated message
    $encryptedMessage = Crypto::encrypt($data['message']);

    // Update message
    $stmt = $con->prepare("
        UPDATE message 
        SET content = :message
        WHERE id = :mid
    ");

    $stmt->bindParam(":message", $encryptedMessage);
    $stmt->bindParam(":mid", $data['message_id']);

    $stmt->execute();

    echo json_encode([
        "status" => "OK",
        "message" => "Message updated successfully"
    ]);

} catch(Exception $e) {

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>