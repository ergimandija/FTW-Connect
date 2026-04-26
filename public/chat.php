<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="assets/js/chat.js" defer></script>
    
</head>

<!---- lines To be removed in later updates -->
<?php
include '../config/config.php';
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

<!-------------------------------------------->
<body>
    <h1>Chat </h1>
    <div style="padding:2px; margin: 10px; height: 200px;" class="overflow-auto" id="messageContainer">

    </div>
    <form id="chatForm" >
        <input type="text" id="message" placeholder="send message"> 
        <input type="hidden" id="chatId" value="<?=$_GET['cid'];?>">
        <input type="hidden" id="uid_reference" value="<?=$_SESSION['id']?>">
        <input type="hidden" id="loadCount" value="1">
        <input type="submit">
    </form>
</body>
</html>

