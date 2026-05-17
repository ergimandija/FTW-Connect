<?php
require '../../config/config.php';

header('Content-Type: application/json');

$stmt = $con->prepare("SELECT DISTINCT chat_id, archived FROM chat_user WHERE user_id=:id");
$stmt->bindParam(":id", $_SESSION['uid']);
$stmt->execute();
$idList = $stmt->fetchAll(PDO::FETCH_ASSOC);

function getRecipientName($con, $chatId, $uid){
    $stmt = $con->prepare("
        SELECT u.name 
        FROM users u 
        JOIN chat_user cu ON cu.user_id = u.id  
        WHERE cu.chat_id = :chatId AND cu.user_id != :uid 
        LIMIT 1
    ");
    $stmt->bindParam(":chatId", $chatId);
    $stmt->bindParam(":uid", $uid);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['name'] ?? 'Unknown';
}

$result = [];

foreach($idList as $chatId){
    $stmt = $con->prepare("SELECT * FROM chat WHERE id=:id");
    $stmt->bindParam(":id", $chatId["chat_id"]);
    $stmt->execute();
    $chatData = $stmt->fetch(PDO::FETCH_ASSOC);

    $chatName = !empty($chatData["name"]) 
        ? $chatData["name"] 
        : getRecipientName($con, $chatData["id"], $_SESSION['uid']);

    $result[] = [
        "id" => $chatData["id"],
        "name" => $chatName,
        "picture" => $chatData["picture"] ?? "./assets/img/anonymous.png",
        "description" => $chatData["description"] ?? "",
        "archived" => $chatId["archived"]
    ];
}

echo json_encode($result);