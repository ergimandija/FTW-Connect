
<!DOCTYPE html>
<html lang="en">
<?php 
include '../src/includes/header.php';
$stmt = $con->prepare("SELECT distinct chat_id from chat_user where user_id=:id");
$stmt->bindParam(":id", $_SESSION['id']);
$stmt->execute();
$idList = $stmt->fetchAll();

function getRecipientName($con, $id){
        $stmt = $con->prepare("SELECT u.name from users as u join chat_user as cu on cu.user_id = u.id  where cu.chat_id = :chatId  and cu.user_id != :currentUserId limit 1");
        $stmt->bindParam(":currentUserId", $_SESSION['id']);
        $stmt->bindParam(":chatId", $id);
        $stmt->execute();
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['name'];;
        
}
?>

<div>    
<?php
foreach($idList as $chatId){
     $stmt = $con->prepare("SELECT * from chat where id=:id");
     $stmt->bindParam(":id", $chatId["chat_id"]);
     $stmt->execute();
     $chatData = $stmt->fetch(PDO::FETCH_ASSOC);
     $chatName = (!empty($chatData["name"]))?$chatData["name"]: getRecipientName($con, $chatData["id"]);
     $chatPicturePath = (!empty($chatData["picture"]))?$chatData["picture"]: "./assets/img/anonymous.png";
     $description = (!empty($chatData["description"]))?$chatData["description"]: "";

?>
    <div style="border: solid; display:flex;">
        <img src=<?=$chatPicturePath ?> width="30px" height="30px">
        <a href=<?="./chat.php?cid=".$chatId["chat_id"]?>> <?=$chatName ?> </a>
        <p><?=$description ?><p>
</div>
<?php     
 }
?>
</div>

<p>
    
</body>
</html>
