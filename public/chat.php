<!DOCTYPE html>
<html lang="en">
<?php 
    include '../src/includes/header.php';
    ?> 
   <script src="assets/js/chat.js" defer></script>
<body>
    <h1>Chat </h1>
    <div style="padding:2px; margin: 10px; height: 200px;" class="overflow-auto" id="messageContainer">

    </div>
    <form id="chatForm" >
        <input type="text" id="message" placeholder="send message"> 
        <input type="hidden" id="chatId" value="<?=$_GET['cid'];?>">
        <input type="hidden" id="uid_reference" value="<?=$_SESSION['uid']?>">
        <input type="hidden" id="loadCount" value="1">
        <input type="submit">
    </form>
</body>
</html>

