<?php
header('Content-Type: application/json');
include '../../config/config.php';


if(isset($_GET['id'])){
    $stmt = $con->prepare('delete from message where id=:id');
    $stmt->bindParam(":id",$_GET['id']);
    $stmt->execute();
}
?>