<?php
    ob_start();
    require_once '../src/includes/header.php';
    ob_end_clean();

    include '../src/auth/auth_check.php';

    $isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["uid"]);

    header("Content-Type: application/json");

    $search = trim($_GET["search"] ?? "");
    $offset = (int)($_GET["offset"] ?? 0);
    $limit = (int)($_GET["limit"] ?? 5);

    if ($offset < 0)
    {
        $offset = 0;
    }

    if ($limit < 1)
    {
        $limit = 5;
    }

    if ($limit > 20)
    {
        $limit = 20;
    }

    $fetchLimit = $limit + 1;

    $sql = "
    SELECT 
        post.id,
        post.user_id,
        post.title,
        post.content,
        post.image_url,
        post.created_at,
        users.name,
        users.profilePicturePath,
        COUNT(DISTINCT comment.id) AS comment_count,
        COUNT(DISTINCT post_likes.id) AS like_count,
        MAX(CASE WHEN post_likes.user_id = :current_user_id THEN 1 ELSE 0 END) AS user_liked
    FROM post
    LEFT JOIN users ON post.user_id = users.id
    LEFT JOIN comment ON comment.post_id = post.id
    LEFT JOIN post_likes ON post_likes.post_id = post.id
    ";

    if ($search !== "")
    {
        $sql .= "
        WHERE post.title LIKE :search
        OR post.content LIKE :search
        ";
    }

    $sql .= "
    GROUP BY post.id, post.user_id, post.title, post.content, post.image_url, post.created_at, users.name, users.profilePicturePath
    ORDER BY post.created_at DESC
    LIMIT :limit OFFSET :offset
    ";

    $postsStmt = $con->prepare($sql);

    $currentUserId = $isLoggedIn ? $_SESSION["uid"] : 0;
    $postsStmt->bindParam(":current_user_id", $currentUserId, PDO::PARAM_INT);
    $postsStmt->bindParam(":limit", $fetchLimit, PDO::PARAM_INT);
    $postsStmt->bindParam(":offset", $offset, PDO::PARAM_INT);

    if ($search !== "")
    {
        $searchTerm = "%" . $search . "%";
        $postsStmt->bindParam(":search", $searchTerm);
    }

    $postsStmt->execute();
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

    $hasMorePosts = count($posts) > $limit;

    if ($hasMorePosts)
    {
        array_pop($posts);
    }

    ob_start();

    foreach ($posts as $post)
    {
        include 'post_card.php';
    }

    $html = ob_get_clean();

    echo json_encode([
        "html" => $html,
        "hasMorePosts" => $hasMorePosts,
        "nextOffset" => $offset + count($posts)
    ]);
    exit();
?>