<?php
    require_once '../config/config.php';

    header('Content-Type: application/json');

    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["uid"]))
    {
        echo json_encode([
            "success" => false,
            "message" => "Not logged in"
        ]);
        exit();
    }

    if (!isset($_POST["post_id"]))
    {
        echo json_encode([
            "success" => false,
            "message" => "Missing post id"
        ]);
        exit();
    }

    $postId = $_POST["post_id"];
    $userId = $_SESSION["uid"];

    $checkStmt = $con->prepare("
        SELECT id
        FROM post_likes
        WHERE post_id = :post_id
        AND user_id = :user_id
    ");
    $checkStmt->bindParam(":post_id", $postId);
    $checkStmt->bindParam(":user_id", $userId);
    $checkStmt->execute();

    $existingLike = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if ($existingLike)
    {
        $deleteStmt = $con->prepare("
            DELETE FROM post_likes
            WHERE post_id = :post_id
            AND user_id = :user_id
        ");
        $deleteStmt->bindParam(":post_id", $postId);
        $deleteStmt->bindParam(":user_id", $userId);
        $deleteStmt->execute();

        $liked = false;
    }
    else
    {
        $insertStmt = $con->prepare("
            INSERT INTO post_likes (post_id, user_id)
            VALUES (:post_id, :user_id)
        ");
        $insertStmt->bindParam(":post_id", $postId);
        $insertStmt->bindParam(":user_id", $userId);
        $insertStmt->execute();

        $liked = true;
    }

    $countStmt = $con->prepare("
        SELECT COUNT(*) AS like_count
        FROM post_likes
        WHERE post_id = :post_id
    ");
    $countStmt->bindParam(":post_id", $postId);
    $countStmt->execute();

    $result = $countStmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "liked" => $liked,
        "like_count" => $result["like_count"]
    ]);
    exit();
?>