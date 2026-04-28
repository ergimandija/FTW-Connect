<!DOCTYPE html>
<html lang="en">

<?php
    require_once '../src/includes/header.php';

    $isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["uid"]);

    if ($isLoggedIn && isset($_POST["create_post"]))
    {
        $imagePath = null;
        $postContent = trim($_POST["post_content"]);
        $userId = $_SESSION["uid"];
        $title = "Post";

        if (!empty($_FILES["post_image"]["tmp_name"]) && is_uploaded_file($_FILES["post_image"]["tmp_name"]))
        {
            $uploadDir = __DIR__ . "/assets/uploads/posts/";
            $fileExtension = strtolower(pathinfo($_FILES["post_image"]["name"], PATHINFO_EXTENSION));
            $fileName = uniqid("post_", true) . "." . $fileExtension;
            $tmpName = $_FILES["post_image"]["tmp_name"];
            $fileSize = $_FILES["post_image"]["size"];
            $fileMime = mime_content_type($tmpName);

            $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
            $allowedMimeTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            $maxFileSize = 5 * 1024 * 1024;

            if
            (
                $_FILES["post_image"]["error"] === UPLOAD_ERR_OK &&
                $fileSize > 0 &&
                $fileSize <= $maxFileSize &&
                in_array($fileExtension, $allowedExtensions) &&
                in_array($fileMime, $allowedMimeTypes)
            )
            {
                $imagePath = "assets/uploads/posts/" . $fileName;
                move_uploaded_file($tmpName, $uploadDir . $fileName);
            }
        }

        if ($postContent !== "" || $imagePath !== null)
        {
            $sql = "INSERT INTO post (title, content, image_url, user_id)
            VALUES (:title, :content, :image_url, :user_id)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":content", $postContent);
            $stmt->bindParam(":image_url", $imagePath);
            $stmt->bindParam(":user_id", $userId);
            $stmt->execute();

            header("Location: feed.php");
            exit();
        }
    }

    
    if ($isLoggedIn && isset($_POST["create_comment"]))
    {
        $commentContent = trim($_POST["comment_content"]);
        $postId = $_POST["post_id"];
        $userId = $_SESSION["uid"];

        if ($commentContent !== "")
        {
            $sql = "INSERT INTO comment (content, user_id, post_id)
                    VALUES (:content, :user_id, :post_id)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(":content", $commentContent);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":post_id", $postId);
            $stmt->execute();

            header("Location: feed.php");
            exit();
        }
    }


    $postsStmt = $con->prepare("
        SELECT 
            post.id,
            post.content,
            post.image_url,
            post.created_at,
            users.name,
            COUNT(comment.id) AS comment_count
        FROM post
        LEFT JOIN users ON post.user_id = users.id
        LEFT JOIN comment ON comment.post_id = post.id
        GROUP BY post.id, post.content, post.image_url, post.created_at, users.name
        ORDER BY post.created_at DESC
    ");
    $postsStmt->execute();
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
?>



<body>
    <?php
        include '../src/includes/navbar.php';
    ?>
     <div class="container my-4">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <img src="" class="rounded-circle mb-3" alt="Profile Picture">
                        <h5 class="card-title mb-1">Max Mustermann</h5>
                        <p class="text-muted small">Student at FH Technikum Wien</p>
                        <a href="#" class="btn btn-outline-primary btn-sm w-100">View Profile</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Create Post</h5>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <textarea class="form-control" name="post_content" rows="3" placeholder="What's on your mind?"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload image</label>
                                <input type="file" class="form-control" name="post_image">
                            </div>
                            <button type="submit" name="create_post" class="btn btn-primary">Post</button>
                        </form>
                    </div>
                </div>
                <?php
                    foreach ($posts as $post)
                    {
                ?>
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="" class="rounded-circle me-3" alt="Profile Picture">
                                    <div>
                                        <h6 class="mb-0"><?php echo htmlspecialchars($post["name"] ?? "Unknown User"); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($post["created_at"]); ?></small>
                                    </div>
                                </div>

                                <?php
                                    if (!empty($post["content"]))
                                    {
                                ?>
                                        <p class="card-text">
                                        <?php echo nl2br(htmlspecialchars($post["content"])); ?>
                                        </p>
                                <?php
                                    }
                                ?>

                                <?php
                                    if (!empty($post["image_url"]))
                                    {
                                ?>
                                        <img src="<?php echo htmlspecialchars($post["image_url"]); ?>" class="img-fluid rounded mb-3" alt="Post Image">
                                <?php
                                    }
                                ?>

                                <div class="d-flex gap-2">
                                    <button class="btn btn-outline-primary btn-sm">Like</button>
                                    <button class="btn btn-outline-secondary btn-sm" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                                            <path d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105"/>
                                        </svg>
                                        <?php echo htmlspecialchars($post["comment_count"]); ?>
                                    </button>
                                </div>
                                

                                <?php
                                    $commentsStmt = $con->prepare("
                                        SELECT comment.content, comment.created_at, users.name
                                        FROM comment
                                        LEFT JOIN users ON comment.user_id = users.id
                                        WHERE comment.post_id = :post_id
                                        ORDER BY comment.created_at ASC
                                    ");
                                    $commentsStmt->bindParam(":post_id", $post["id"]);
                                    $commentsStmt->execute();
                                    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);
                                ?>

                                <?php
                                    foreach ($comments as $comment)
                                    {
                                ?>
                                        <div class="mt-3 p-2 bg-light rounded">
                                            <strong><?php echo htmlspecialchars($comment["name"] ?? "Unknown User"); ?></strong>
                                            <p class="mb-1"><?php echo nl2br(htmlspecialchars($comment["content"])); ?></p>
                                            <small class="text-muted"><?php echo htmlspecialchars($comment["created_at"]); ?></small>
                                        </div>
                                <?php
                                    }
                                ?>



                                <?php
                                    if ($isLoggedIn)
                                    {
                                ?>
                                        <form action="" method="post" class="mt-3">
                                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post["id"]); ?>">
                                            <div class="input-group">
                                                <input 
                                                    type="text" 
                                                    name="comment_content" 
                                                    class="form-control" 
                                                    placeholder="Write a comment..."
                                                    required
                                                >
                                                <button type="submit" name="create_comment" class="btn btn-outline-secondary">
                                                    Send
                                                </button>
                                            </div>
                                        </form>
                                <?php
                                    }
                                    else
                                    {
                                ?>
                                    <p class="text-muted small mt-3">
                                        Log in to comment.
                                    </p>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                <?php
                    }
                ?>
                    </div>
                        <div class="col-lg-3 mb-4">
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <h5 class="card-title">Trends</h5>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">#FHTechnikum</li>
                                        <li class="list-group-item">#WebDevelopment</li>
                                        <li class="list-group-item">#StudentLife</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">Suggestions</h5>
                                    <p class="mb-2">Connect with other students.</p>
                                    <a href="#" class="btn btn-outline-primary btn-sm w-100">Find People</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
</body>
</html>