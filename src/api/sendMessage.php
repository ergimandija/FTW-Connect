<?php 
header('Content-Type: application/json');
include '../../config/config.php';


try{ 
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    throw new Exception("Invalid JSON");
}

if (!isset($_SESSION['uid'])) {
    throw new Exception("No session id");
}

if (empty($data['cid']) || empty($data['message'])) {
    throw new Exception("Missing fields");
}

$stmt = $con->prepare('SELECT * FROM chat_user where chat_id = :chid AND user_id = :uid');
$stmt->bindParam(":chid" , $data['cid']);
$stmt->bindParam(":uid",$_SESSION['uid']);
$stmt->execute();
$check = $stmt->fetch(PDO::FETCH_ASSOC);
if(!empty($check['chat_id'])){
$stmt = $con->prepare('INSERT INTO MESSAGE(chat_id,sender_id,content) VALUES(:chatId,:id,:message)');
$stmt->bindParam(":chatId", $data['cid']);
$stmt->bindParam(":id", $_SESSION['uid']);
$stmt->bindParam(":message", $data['message']);
$stmt->execute();
echo  json_encode([
        "status" => "OK",
        "message" => "message sent successfully"
    ]);
} else {
    echo  json_encode([
        "status" => "error",
        "message" => "user is not in the chat"
    ]);
} 
}
catch(Exception $e){
    echo  json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}

?>