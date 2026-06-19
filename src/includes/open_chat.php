<?php
if(isset($_GET['message_user'])){

    $target_id = $_GET['message_user'];
    $own_id = $_SESSION['uid'];

    $stmt = $con->prepare("
        SELECT c.id
        FROM chat c
        JOIN chat_user cu1 ON c.id = cu1.chat_id
        JOIN chat_user cu2 ON c.id = cu2.chat_id
        WHERE cu1.type = 'private' 
        AND cu1.user_id = :own_id
        AND cu2.user_id = :target_id
        LIMIT 1
    ");

    $stmt->bindParam(":own_id", $own_id);
    $stmt->bindParam(":target_id", $target_id);

    $stmt->execute();

    $chat = $stmt->fetch(PDO::FETCH_ASSOC);

    if($chat){
        header("Location: userchats.php?cid=".$chat['id']);
        exit();
    } else {

        $stmt = $con->prepare("
            INSERT INTO chat
            VALUES()
        ");

        $stmt->execute();

        $chat_id = $con->lastInsertId();

        $stmt = $con->prepare("
            INSERT INTO chat_user(chat_id,user_id,type)
            VALUES(:chat_id,:user_id,'private')
        ");

        $stmt->execute([
            ':chat_id'=>$chat_id,
            ':user_id'=>$own_id
        ]);

        $stmt->execute([
            ':chat_id'=>$chat_id,
            ':user_id'=>$target_id
        ]);

        header("Location:  userchats.php?cid=".$chat_id);
        exit();
    }
}

?>