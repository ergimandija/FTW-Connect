<!DOCTYPE html>
<?php require_once __DIR__ . '/../config/config.php'; ?>
<?php require_once __DIR__ . '/../src/auth/login.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FTW Connect</title>

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
                    <img src="assets/Logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Welcome back</h3>
                <p class="text-muted mb-4">Please sign in to your account.</p>

                <div class="text-center mb-3">
                    <?php 
                    if (!empty($errors)) { 
                        foreach ($errors as $error) { 
                            echo "<p class='text-danger'>" . htmlspecialchars($error) . "</p>"; 
                        } 
                    } 
                    ?>
                </div>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">EMAIL ADDRESS</label>
                        <input type="email" name="email" class="form-control" placeholder="uid@technikum-wien.at" required>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label text-secondary small fw-bold">PASSWORD</label>
                            <a href="forgot_password.php" class="small accent-text fw-normal">Forgot?</a>
                        </div>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Sign In
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    Don't have an account? <a href="register.php" class="accent-text">Register now</a>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>