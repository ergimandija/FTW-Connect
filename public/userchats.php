
<!---- lines To be removed in later updates -->
<?php
include '../config/config.php';
session_start();
$_SESSION['id'] = 1;

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<!-------------------------------------------->


<?php
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



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>


<p>
<div >    
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
