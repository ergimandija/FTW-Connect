<!DOCTYPE html>
<?php require_once __DIR__ . '/../src/db/db.php'; ?>
<?php require_once __DIR__ . '/../src/auth/file_upload.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share File | FTW Connect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/login.css">
</head>

<body>
<div class="container-fluid h-100">
    <div class="row h-100">

        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center left-side">
            <div class="logo-box">
                <img src="assets/Logo.png" alt="FTW Connect Logo" class="img-fluid logo-img">
            </div>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 420px; padding: 20px;">

                <div class="text-center d-md-none mb-4">
                    <img src="assets/Logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Share a file</h3>
                <p class="text-muted mb-4">Upload a file to share with your group.</p>

                <div class="text-center mb-3">
                    <?php
                    if (!empty($errors)) {
                        foreach ($errors as $error) {
                            echo "<p class='text-danger'>" . htmlspecialchars($error) . "</p>";
                        }
                    }
                    if (!empty($success)) {
                        echo "<p class='text-success'>" . htmlspecialchars($success) . "</p>";
                    }
                    ?>
                </div>

                <form method="POST" action="upload_file.php?cid=<?= (int)$chatId ?>" enctype="multipart/form-data">

                    <input type="hidden" name="cid" value="<?= (int)$chatId ?>">

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">FILE</label>
                        <input
                            type="file"
                            name="file"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.txt,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.mp4,.mp3"
                            required
                        >
                        <div class="form-text">
                            Max 10MB. Allowed: images, pdf, Word, Excel, PowerPoint, txt, zip, mp4, mp3.
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Upload File
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    <?php if ($chatId): ?>
                        <a href="chat.php?cid=<?= (int)$chatId ?>" class="accent-text">Back to group</a>
                    <?php else: ?>
                        <a href="userchats.php" class="accent-text">Back to chats</a>
                    <?php endif; ?>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>