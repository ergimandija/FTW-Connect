
<?php
    require_once "../config/config.php";

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

    $postsStmt = $con->prepare("
        SELECT post.content, post.image_url, post.created_at, users.name
        FROM post
        LEFT JOIN users ON post.user_id = users.id
        ORDER BY post.created_at DESC
    ");
    $postsStmt->execute();
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>FTW-Connect</title>
</head>
<body>
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
                                    <button class="btn btn-outline-secondary btn-sm">Comment</button>
                                </div>
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