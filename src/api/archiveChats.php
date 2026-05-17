<?php
require '../../config/config.php';

header('Content-Type: application/json');

if(isset($_GET['cid_arc']) && isset($_GET['status'])){
    try {
        $stmt = $con->prepare("update chat_user set archived=:status where user_id=:uid and chat_id=:cid");
        $stmt->bindParam(":uid", $_SESSION['uid']);
        $stmt->bindParam(":cid", $_GET['cid_arc']);
        $stmt->bindParam(":status", $_GET['status']);
        $stmt->execute();
        echo(json_encode([
            "status" => "success",
            "message" => "chat archived successfully"
        ]));
    } catch(PDOException $e){
        echo(json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]));
    }
        
}


?>