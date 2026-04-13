<!DOCTYPE html>
<?php require_once __DIR__ . '/../src/db/db.php'; ?>
<?php require_once __DIR__ . '/../src/auth/reset_password.php'; ?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | FTW Connect</title>

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
            <div style="width: 100%; max-width: 380px; padding: 20px;">

                <div class="text-center d-md-none mb-4">
                    <img src="assets/Logo.png" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Reset your password</h3>
                <p class="text-muted mb-4">Enter a new password.</p>

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

                <?php if (!$tokenValid): ?>
                    <p class="text-danger text-center">Invalid or expired token.</p>
                <?php else: ?>
                <form method="POST">

                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold">NEW PASSWORD</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">CONFIRM PASSWORD</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Reset Password
                        </button>
                    </div>
                </form>
                <?php endif; ?>

                <p class="mt-4 text-center">
                    <a href="login.php" class="accent-text">Back to login</a>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>