<?php
header('Content-Type: application/json');
include '../../config/config.php';


if(isset($_GET['cid'])){
 try{
    $stmt = $con->prepare('SELECT * FROM chat_user where chat_id = :chid AND user_id = :uid');
    $stmt->bindParam(":chid" , $_GET['cid']);
    $stmt->bindParam(":uid",$_SESSION['id']);
    $stmt->execute();
    $check = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($check['chat_id'])){
        $stmt = $con->prepare('SELECT * FROM message where chat_id = :chid order by sent_at asc');
        $stmt->bindParam(":chid" , $_GET['cid']);
        $stmt->execute();
        $messages = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        echo $messages;
        
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "user is not in the chat"
        ]);
    }
 } catch(Exception $e){
     echo  json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
 }

}


?>