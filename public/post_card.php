<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <?php
                $postUserImage = "assets/img/anonymous.png";

                if (!empty($post["profilePicturePath"]))
                {
                    $postUserImage = $post["profilePicturePath"];
                }
            ?>

            <img 
                src="<?php echo htmlspecialchars($postUserImage); ?>" 
                class="rounded-circle me-3" 
                alt="Profile Picture"
                width="48"
                height="48"
            >
            <div>
                <h6 class="mb-0">
                    <a 
                        href="profile.php?id=<?php echo htmlspecialchars($post["user_id"]); ?>" 
                        class="text-decoration-none text-dark"
                    >
                        <?php echo htmlspecialchars($post["name"] ?? "Unknown User"); ?>
                    </a>
                </h6>
                <small class="text-muted"><?php echo htmlspecialchars($post["created_at"]); ?></small>
            </div>
        </div>
        <h5 class="card-title">
            <?php echo htmlspecialchars($post["title"]); ?>
        </h5>
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
            if (!empty($post["image_url"]) && file_exists(__DIR__ . "/" . $post["image_url"]))
            {
        ?>
                <img src="<?php echo htmlspecialchars($post["image_url"]); ?>" class="img-fluid rounded mb-3" alt="Post Image">
        <?php
            }
        ?>

        <div class="d-flex gap-2">
            <?php
                if ($isLoggedIn)
                {
            ?>
                <button 
                    class="btn btn-sm like-button <?php echo $post["user_liked"] ? "btn-primary" : "btn-outline-primary"; ?>"
                    type="button"
                    data-post-id="<?php echo htmlspecialchars($post["id"]); ?>"
                >
                    Like <span class="like-count"><?php echo htmlspecialchars($post["like_count"]); ?></span>
                </button>
            <?php
                }
                else
                {
            ?>
                <button class="btn btn-outline-primary btn-sm" type="button" disabled>
                    Like <span><?php echo htmlspecialchars($post["like_count"]); ?></span>
                </button>
            <?php
                }
            ?>
            <button 
                class="btn btn-outline-secondary btn-sm comment-jump-button" 
                type="button"
                data-post-id="<?php echo htmlspecialchars($post["id"]); ?>"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chat" viewBox="0 0 16 16">
                    <path d="M2.678 11.894a1 1 0 0 1 .287.801 11 11 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8 8 0 0 0 8 14c3.996 0 7-2.807 7-6s-3.004-6-7-6-7 2.808-7 6c0 1.468.617 2.83 1.678 3.894m-.493 3.905a22 22 0 0 1-.713.129c-.2.032-.352-.176-.273-.362a10 10 0 0 0 .244-.637l.003-.01c.248-.72.45-1.548.524-2.319C.743 11.37 0 9.76 0 8c0-3.866 3.582-7 8-7s8 3.134 8 7-3.582 7-8 7a9 9 0 0 1-2.347-.306c-.52.263-1.639.742-3.468 1.105"/>
                </svg>
                <?php echo htmlspecialchars($post["comment_count"]); ?>
            </button>
            <?php
                if ($isLoggedIn && $post["user_id"] == $_SESSION["uid"])
                {
            ?>
                <form action="" method="post" onsubmit="return confirm('Do you really want to delete this post?');">
                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post["id"]); ?>">
                    <button type="submit" name="delete_post" class="btn btn-outline-danger btn-sm">
                        Delete
                    </button>
                </form>
            <?php
                }
            ?>
        </div>
        

        <?php
            $commentsStmt = $con->prepare("
                SELECT comment.id, comment.user_id, comment.content, comment.created_at, users.name, users.profilePicturePath
                FROM comment
                LEFT JOIN users ON comment.user_id = users.id
                WHERE comment.post_id = :post_id
                AND comment.parent_comment_id IS NULL
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
                    <?php
                        $commentUserImage = "assets/img/anonymous.png";

                        if (!empty($comment["profilePicturePath"]))
                        {
                            $commentUserImage = $comment["profilePicturePath"];
                        }
                    ?>

                    <div class="d-flex align-items-start">
                        <img 
                            src="<?php echo htmlspecialchars($commentUserImage); ?>" 
                            class="rounded-circle me-2" 
                            alt="Profile Picture"
                            width="32"
                            height="32"
                        >

                        <div>
                            <strong>
                                <a 
                                    href="profile.php?id=<?php echo htmlspecialchars($comment["user_id"]); ?>" 
                                    class="text-decoration-none text-reset"
                                >
                                    <?php echo htmlspecialchars($comment["name"] ?? "Unknown User"); ?>
                                </a>
                            </strong>

                            <p class="mb-1">
                                <?php echo nl2br(htmlspecialchars($comment["content"])); ?>
                            </p>

                            <small class="text-muted">
                                <?php echo htmlspecialchars($comment["created_at"]); ?>
                            </small>
                        </div>
                    </div>

                    <?php
                        if ($isLoggedIn && $comment["user_id"] == $_SESSION["uid"])
                        {
                    ?>
                            <form action="" method="post" class="mt-1" onsubmit="return confirm('Do you really want to delete this comment?');">
                                <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment["id"]); ?>">
                                    <button type="submit" name="delete_comment" class="btn btn-outline-danger btn-sm">
                                        Delete
                                    </button>
                            </form>
                    <?php
                        }
                    ?>

                    <?php
                        $repliesStmt = $con->prepare("
                            SELECT comment.id, comment.user_id, comment.content, comment.created_at, users.name, users.profilePicturePath
                            FROM comment
                            LEFT JOIN users ON comment.user_id = users.id
                            WHERE comment.parent_comment_id = :parent_comment_id
                            ORDER BY comment.created_at ASC
                        ");
                        $repliesStmt->bindParam(":parent_comment_id", $comment["id"]);
                        $repliesStmt->execute();
                        $replies = $repliesStmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <?php
                        foreach ($replies as $reply)
                        {
                    ?>
                            <div class="mt-2 ms-4 p-2 bg-white border rounded">
                                <?php
                                    $replyUserImage = "assets/img/anonymous.png";

                                    if (!empty($reply["profilePicturePath"]))
                                    {
                                        $replyUserImage = $reply["profilePicturePath"];
                                    }
                                ?>

                                <div class="d-flex align-items-start">
                                    <img 
                                        src="<?php echo htmlspecialchars($replyUserImage); ?>" 
                                        class="rounded-circle me-2" 
                                        alt="Profile Picture"
                                        width="28"
                                        height="28"
                                    >

                                    <div>
                                        <strong>
                                            <a 
                                                href="profile.php?id=<?php echo htmlspecialchars($reply["user_id"]); ?>" 
                                                class="text-decoration-none text-reset"
                                            >
                                                <?php echo htmlspecialchars($reply["name"] ?? "Unknown User"); ?>
                                            </a>
                                        </strong>

                                        <p class="mb-1">
                                            <?php echo nl2br(htmlspecialchars($reply["content"])); ?>
                                        </p>

                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($reply["created_at"]); ?>
                                        </small>
                                    </div>
                                </div>

                                <?php
                                    if ($isLoggedIn && $reply["user_id"] == $_SESSION["uid"])
                                    {
                                ?>
                                        <form action="" method="post" class="mt-1" onsubmit="return confirm('Do you really want to delete this reply?');">
                                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($reply["id"]); ?>">
                                            <button type="submit" name="delete_comment" class="btn btn-outline-danger btn-sm">
                                                Delete
                                            </button>
                                        </form>
                                <?php
                                    }
                                ?>

                            </div>
                    <?php
                        }
                    ?>

                    <?php
                        if ($isLoggedIn)
                        {
                    ?>
                            <form action="" method="post" class="mt-2 ms-4">
                                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post["id"]); ?>">
                                <input type="hidden" name="parent_comment_id" value="<?php echo htmlspecialchars($comment["id"]); ?>">

                                <div class="input-group input-group-sm">
                                    <input 
                                        type="text" 
                                        name="comment_content" 
                                        class="form-control" 
                                        placeholder="Reply to this comment..."
                                        required
                                    >
                                    <button type="submit" name="create_comment" class="btn btn-outline-secondary">
                                        Reply
                                    </button>
                                </div>
                            </form>
                    <?php
                        }
                    ?>
                </div>
        <?php
            }
        ?>



        <?php
            if ($isLoggedIn)
            {
        ?>
                <form 
                    action="" 
                    method="post" 
                    class="mt-3" 
                    id="comment-form-<?php echo htmlspecialchars($post["id"]); ?>"
                >
                    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post["id"]); ?>">
                    <div class="input-group">
                        <input 
                            type="text" 
                            name="comment_content" 
                            class="form-control" 
                            id="comment-input-<?php echo htmlspecialchars($post["id"]); ?>"
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