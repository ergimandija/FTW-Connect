<!DOCTYPE html>
<html lang="en">

<?php include '../src/includes/header.php';?>
<?php require_once __DIR__ . '/../src/groups/edit_group.php'; ?>
<link rel="stylesheet" href="./assets/styles/create_group.css">

<body>
<div class="container-fluid h-100">
    <div class="row h-100">

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 420px; padding: 20px;">

                <?php if ($accessDenied): ?>
                    <div class="alert alert-danger">
                        You do not have permission to edit this group.
                    </div>
                    <a href="chat.php?cid=<?= $cid ?>" class="btn btn-secondary">Back to Chat</a>
                <?php else: ?>

                <div class="text-center d-md-none mb-4">
                    <img src="assets/img/logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Edit Group</h3>
                <p class="text-muted mb-4">Update your group's name, description, or picture.</p>

                <div class="text-center mb-3">
                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <p class="text-danger"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <p class="text-success"><?= htmlspecialchars($success) ?></p>
                    <?php endif; ?>
                </div>

                <?php if ($groupPicture): ?>
                <div class="text-center mb-3">
                    <img src="<?= htmlspecialchars($groupPicture) ?>" alt="Group Picture"
                         style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                    <p class="text-muted small mt-1">Current picture</p>
                </div>
                <?php endif; ?>

                <form method="POST" action="edit_group.php?cid=<?= $cid ?>" enctype="multipart/form-data">

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
                        <div class="form-text">Max 2MB. Allowed: jpg, jpeg, png, gif, webp. Leave empty to keep current picture.</div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Save Changes
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    <a href="chat.php?cid=<?= $cid ?>" class="accent-text">Back to chat</a>
                </p>

                <?php endif; ?>

            </div>
        </div>

    </div>
</div>
</body>
</html>
