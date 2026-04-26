<?php
header('Content-Type: application/json');
include '../../config/config.php';


if(isset($_GET['cid'])){
 try{
    $limit = 25;
    $stmt = $con->prepare('SELECT * FROM chat_user where chat_id = :chid AND user_id = :uid');
    $stmt->bindParam(":chid" , $_GET['cid']);
    $stmt->bindParam(":uid",$_SESSION['uid']);
    $stmt->execute();
    $check = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!empty($check['chat_id'])){

    $stmt =  $con->prepare('SELECT count(*) from message where chat_id = :chid');
    $stmt->bindParam(":chid",  $_GET['cid']);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    $loadCount = $_GET['loadCount'];
    $limit = min((int)($loadCount * 50), (int)$count);
    $offset = max(0, $count - $limit);
    $stmt = $con->prepare("SELECT * FROM message WHERE chat_id = :chid order by sent_at asc limit ".$limit." offset ".$offset);
        $stmt->bindParam(":chid" , $_GET['cid']);

        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode([
            "messages" => $messages,
            "total" => $count,
            "limit" => $limit
        ]);
        
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