<!DOCTYPE html>
<html lang="en">

<?php include '../src/includes/header.php';?>
<?php require_once __DIR__ . '/../src/auth/create_group.php'; ?>



<body>
<div class="container-fluid h-100">
    <div class="row h-100">

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 420px; padding: 20px;">

                <div class="text-center d-md-none mb-4">
                    <img src="assets/Logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Create a group</h3>
                <p class="text-muted mb-4">Set up a new group to start collaborating.</p>

                <div class="text-center mb-3">
                    <?php
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo "<p class='text-danger'>" . htmlspecialchars($error) . "</p>";
                        }
                    }
                    if (!empty($success)) {
                        echo "<p class='text-success'>" . htmlspecialchars($success) . "</p>";
                        if ($createdChatId) {
                            echo "<a href='chat.php?cid=" . (int)$createdChatId . "' class='btn btn-sm login-btn text-white mt-1'>Go to group</a>";
                        }
                    }
                    ?>
                </div>

                <form method="POST" action="create_group.php" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">GROUP NAME</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="e.g. Project Alpha"
                            maxlength="255"
                            required
                            value="<?= htmlspecialchars($groupName) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">DESCRIPTION <span class="fw-normal">(optional)</span></label>
                        <textarea
                            name="description"
                            class="form-control"
                            placeholder="What is this group about?"
                            rows="3"
                            style="resize: none;"
                        ><?= htmlspecialchars($groupDescription) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">GROUP PICTURE <span class="fw-normal">(optional)</span></label>
                        <input
                            type="file"
                            name="picture"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.gif,.webp"
                        >
                        <div class="form-text">Max 2MB. Allowed: jpg, jpeg, png, gif, webp.</div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Create Group
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    <a href="userchats.php" class="accent-text">Back to chats</a>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>