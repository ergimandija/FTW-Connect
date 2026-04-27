
<!DOCTYPE html>
<html lang="en">
<?php 
include '../src/includes/header.php';
$stmt = $con->prepare("SELECT distinct chat_id from chat_user where user_id=:id");
$stmt->bindParam(":id", $_SESSION['uid']);
$stmt->execute();
$idList = $stmt->fetchAll();

function getRecipientName($con, $id){
        $stmt = $con->prepare("SELECT u.name from users as u join chat_user as cu on cu.user_id = u.id  where cu.chat_id = :chatId  and cu.user_id != :currentUserId limit 1");
        $stmt->bindParam(":currentUserId", $_SESSION['uid']);
        $stmt->bindParam(":chatId", $id);
        $stmt->execute();
        $row =  $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['name'];
}
?>
<script src="assets/js/userchats.js" defer></script>
<div class="p-3 m-3">
        <div class="d-flex" role="search">
                <input class="form-control me-2" type="search" placeholder="Search" id="search-input" aria-label="Search"/>
                <button class="btn btn-outline-success" id="search-btn">Search</button>
        </div>
</div>
<div id="userList-container" class="container mt-3">
<?php
foreach($idList as $chatId){
     $stmt = $con->prepare("SELECT * from chat where id=:id");
     $stmt->bindParam(":id", $chatId["chat_id"]);
     $stmt->execute();
     $chatData = $stmt->fetch(PDO::FETCH_ASSOC);

     $chatName = (!empty($chatData["name"])) ? $chatData["name"] : getRecipientName($con, $chatData["id"]);
     $chatPicturePath = (!empty($chatData["picture"])) ? $chatData["picture"] : "./assets/img/anonymous.png";
     $description = (!empty($chatData["description"])) ? $chatData["description"] : "";
?>
    <div class="card mb-2 shadow-sm">
        <div class="card-body d-flex align-items-center">

            <img src="<?= $chatPicturePath ?>" 
                 class="rounded-circle me-3" 
                 width="50" height="50" 
                 style="object-fit: cover;">

            <div class="flex-grow-1">
                <a href="<?= "./chat.php?cid=".$chatId["chat_id"] ?>" 
                   class="fw-bold text-decoration-none text-dark">
                   <?= $chatName ?>
                </a>
                <p class="mb-0 text-muted small">
                    <?= $description ?>
                </p>
            </div>
             <button class="btn btn-outline-secondary btn-sm ms-auto archive-btn"
                    data-chat-id="<?= $chatId["chat_id"] ?>">
                Archive
            </button>

        </div>
    </div>
<?php } ?>
</div>
    
</body>
</html>
