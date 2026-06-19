<!DOCTYPE html>
<html lang="en">

<?php
   

    require_once '../src/includes/header.php';

     include '../src/auth/auth_check.php';

    $isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true && isset($_SESSION["uid"]);

    $currentUserStmt = $con->prepare("
        SELECT name, profilePicturePath
        FROM users
        WHERE id = :user_id
    ");
    $currentUserStmt->bindParam(":user_id", $_SESSION["uid"]);
    $currentUserStmt->execute();
    $currentUser = $currentUserStmt->fetch(PDO::FETCH_ASSOC);

    if ($isLoggedIn && isset($_POST["create_post"]))
    {
        $imagePath = null;
        $postContent = trim($_POST["post_content"]);
        $userId = $_SESSION["uid"];
        $title = trim($_POST["post_title"]);

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

        if ($title !== "" && ($postContent !== "" || $imagePath !== null))
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
        $parentCommentId = null;

        if (!empty($_POST["parent_comment_id"]))
        {
        $parentCommentId = $_POST["parent_comment_id"];
        }

        if ($commentContent !== "")
        {
            $sql = "INSERT INTO comment (content, user_id, post_id, parent_comment_id)
                VALUES (:content, :user_id, :post_id, :parent_comment_id)";
            $stmt = $con->prepare($sql);
            $stmt->bindParam(":content", $commentContent);
            $stmt->bindParam(":user_id", $userId);
            $stmt->bindParam(":post_id", $postId);
            $stmt->bindParam(":parent_comment_id", $parentCommentId);
            $stmt->execute();

            header("Location: feed.php");
            exit();
        }
    }

    if ($isLoggedIn && isset($_POST["delete_comment"]))
    {
        $commentId = $_POST["comment_id"];
        $userId = $_SESSION["uid"];

        $checkStmt = $con->prepare("
            SELECT id
            FROM comment
            WHERE id = :comment_id
            AND user_id = :user_id
        ");
        $checkStmt->bindParam(":comment_id", $commentId);
        $checkStmt->bindParam(":user_id", $userId);
        $checkStmt->execute();

        $commentToDelete = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($commentToDelete)
        {
            $deleteRepliesStmt = $con->prepare("
                DELETE FROM comment
                WHERE parent_comment_id = :comment_id
            ");
            $deleteRepliesStmt->bindParam(":comment_id", $commentId);
            $deleteRepliesStmt->execute();

            $deleteCommentStmt = $con->prepare("
                DELETE FROM comment
                WHERE id = :comment_id
                AND user_id = :user_id
            ");
            $deleteCommentStmt->bindParam(":comment_id", $commentId);
            $deleteCommentStmt->bindParam(":user_id", $userId);
            $deleteCommentStmt->execute();
        }

        header("Location: feed.php");
        exit();
    }
    
    if ($isLoggedIn && isset($_POST["delete_post"]))
    {
        $postId = $_POST["post_id"];
        $userId = $_SESSION["uid"];

        $checkStmt = $con->prepare("
            SELECT image_url
            FROM post
            WHERE id = :post_id
            AND user_id = :user_id
        ");
        $checkStmt->bindParam(":post_id", $postId);
        $checkStmt->bindParam(":user_id", $userId);
        $checkStmt->execute();

        $postToDelete = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($postToDelete)
        {
            $deleteRepliesStmt = $con->prepare("
                DELETE FROM comment
                WHERE parent_comment_id IN (
                    SELECT id FROM
                    (
                        SELECT id
                        FROM comment
                        WHERE post_id = :post_id
                    ) AS temp
            )");
            $deleteRepliesStmt->bindParam(":post_id", $postId);
            $deleteRepliesStmt->execute();

            $deleteCommentsStmt = $con->prepare("
                DELETE FROM comment
                WHERE post_id = :post_id
            ");
            $deleteCommentsStmt->bindParam(":post_id", $postId);
            $deleteCommentsStmt->execute();

            $deletePostStmt = $con->prepare("
                DELETE FROM post
                WHERE id = :post_id
                AND user_id = :user_id
            ");
            $deletePostStmt->bindParam(":post_id", $postId);
            $deletePostStmt->bindParam(":user_id", $userId);
            $deletePostStmt->execute();

            if (!empty($postToDelete["image_url"]))
            {
                $imageFilePath = __DIR__ . "/" . $postToDelete["image_url"];

                if (file_exists($imageFilePath))
                {
                    unlink($imageFilePath);
                }
            }
        }

        header("Location: feed.php");
        exit();
    }




    $search = trim($_GET["search"] ?? "");

    $postsPerLoad = 5;
    $offset = 0;
    $fetchLimit = $postsPerLoad + 1;

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
    $postsStmt->bindParam(":current_user_id", $currentUserId);
    $postsStmt->bindParam(":limit", $fetchLimit, PDO::PARAM_INT);
    $postsStmt->bindParam(":offset", $offset, PDO::PARAM_INT);

    if ($search !== "")
    {
        $searchTerm = "%" . $search . "%";
        $postsStmt->bindParam(":search", $searchTerm);
    }

    $postsStmt->execute();
    $posts = $postsStmt->fetchAll(PDO::FETCH_ASSOC);

    $hasMorePosts = count($posts) > $postsPerLoad;

    if ($hasMorePosts)
    {
        array_pop($posts);
    }
?>



<body>
    
     <div class="container my-4">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <?php
                            $currentUserImage = "assets/img/anonymous.png";

                            if (!empty($currentUser["profilePicturePath"]))
                            {
                                $currentUserImage = $currentUser["profilePicturePath"];
                            }
                        ?>

                        <img 
                            src="<?php echo htmlspecialchars($currentUserImage); ?>" 
                            class="rounded-circle mb-3" 
                            alt="Profile Picture"
                            width="80"
                            height="80"
                        >

                        <h5 class="card-title mb-1">
                            <?php echo htmlspecialchars($currentUser["name"] ?? "User"); ?>
                        </h5>
                        <a href="profile.php" class="btn btn-outline-primary btn-sm w-100 mt-2">View Profile</a>
                    </div>
                </div>
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="card-title">Search Posts</h5>

                        <form action="feed.php" method="get">
                            <div class="mb-3">
                                <input 
                                    type="text" 
                                    name="search" 
                                    class="form-control" 
                                    placeholder="Search posts..."
                                    value="<?php echo htmlspecialchars($_GET["search"] ?? ""); ?>"
                                >
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                Search
                            </button>

                            <?php
                                if (!empty($_GET["search"]))
                                {
                            ?>
                                    <a href="feed.php" class="btn btn-outline-secondary btn-sm w-100 mt-2">
                                        Clear Search
                                    </a>
                            <?php
                                }
                            ?>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <?php
                    if ($isLoggedIn)
                    {
                ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Create Post</h5>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    name="post_title" 
                                    placeholder="Post title"
                                    required
                                >
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" name="post_content" rows="3" placeholder="What's on your mind?"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="post_image" class="form-label">Upload image</label>
                                <input 
                                    type="file" 
                                    class="form-control" 
                                    name="post_image"
                                    id="post_image"
                                >
                            </div>
                            <button type="submit" name="create_post" class="btn btn-primary">Post</button>
                        </form>
                    </div>
                </div>
                    <?php
                        if ($search !== "")
                        {
                    ?>
                            <div class="alert alert-light border shadow-sm">
                                Search results for: <?php echo htmlspecialchars($search); ?>
                            </div>
                    <?php
                        }
                    ?>

                    <?php
                        if (empty($posts))
                        {
                    ?>
                            <div class="card shadow-sm mb-4">
                                <div class="card-body text-center">
                                    <p class="mb-0">No posts found.</p>
                                </div>
                            </div>
                    <?php
                        }
                    ?>

                <?php
                    }
                ?>

                <div id="posts-container">
                    <?php
                        foreach ($posts as $post)
                        {
                            include 'post_card.php';
                        }
                    ?>
                </div>

                <?php
                    if ($hasMorePosts)
                    {
                ?>
                    <button 
                        type="button" 
                        id="show-more-button" 
                        class="btn btn-outline-primary w-100 mb-4"
                        data-offset="<?php echo htmlspecialchars($postsPerLoad); ?>"
                        data-limit="<?php echo htmlspecialchars($postsPerLoad); ?>"
                        data-search="<?php echo htmlspecialchars($search); ?>"
                    >
                        Show More
                    </button>
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
                                    <a href="userchats.php" class="btn btn-outline-primary btn-sm w-100">Chats</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <script>
            document.addEventListener("click", function(event) {
                const likeButton = event.target.closest(".like-button");

                if (likeButton) {
                    const postId = likeButton.dataset.postId;

                    fetch("like_post.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "post_id=" + encodeURIComponent(postId)
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            const likeCount = likeButton.querySelector(".like-count");
                            likeCount.textContent = data.like_count;

                            if (data.liked) {
                                likeButton.classList.remove("btn-outline-primary");
                                likeButton.classList.add("btn-primary");
                            } else {
                                likeButton.classList.remove("btn-primary");
                                likeButton.classList.add("btn-outline-primary");
                            }
                        }
                    });

                    return;
                }

                const commentButton = event.target.closest(".comment-jump-button");

                if (commentButton) {
                    const postId = commentButton.dataset.postId;

                    const commentForm = document.getElementById("comment-form-" + postId);
                    const commentInput = document.getElementById("comment-input-" + postId);

                    if (commentForm) {
                        commentForm.scrollIntoView({
                            behavior: "smooth",
                            block: "center"
                        });
                    }

                    if (commentInput) {
                        commentInput.focus();
                    }

                    return;
                }

                const showMoreButton = event.target.closest("#show-more-button");

                if (showMoreButton) {
                    const offset = showMoreButton.dataset.offset;
                    const limit = showMoreButton.dataset.limit;
                    const search = showMoreButton.dataset.search;

                    showMoreButton.disabled = true;
                    showMoreButton.textContent = "Loading...";

                    const url = "load_posts.php?offset="
                        + encodeURIComponent(offset)
                        + "&limit="
                        + encodeURIComponent(limit)
                        + "&search="
                        + encodeURIComponent(search);

                    fetch(url)
                        .then(function(response) {
                            return response.json();
                        })
                        .then(function(data) {
                            const postsContainer = document.getElementById("posts-container");

                            postsContainer.insertAdjacentHTML("beforeend", data.html);

                            showMoreButton.dataset.offset = data.nextOffset;

                            if (!data.hasMorePosts) {
                                showMoreButton.remove();
                            } else {
                                showMoreButton.disabled = false;
                                showMoreButton.textContent = "Show More";
                            }
                        })
                        .catch(function(error) {
                            console.error("Error loading posts:", error);

                            showMoreButton.disabled = false;
                            showMoreButton.textContent = "Show More";
                        });
                }
            });
        </script>
    </body>
</html>