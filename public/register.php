<!DOCTYPE html>
<?php require_once __DIR__ . '/../config/config.php'; ?>
<?php require_once __DIR__ . '/../src/auth/register.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | FTW Connect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/styles/login.css">
</head>

<body>
<div class="container-fluid h-100">
    <div class="row h-100">

        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center left-side m-0 p-0">
            <div class="logo-box w-100 h-100 p-0">
                <img src="assets/img/FH_Technikum_Wien.jpg" alt="FTW Connect Logo" class="w-100 h-100 object-fit-cover">
            </div>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 380px; padding: 20px;">

                <div class="text-center d-md-none mb-4">
                    <img src="assets/img/logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Create an account</h3>
                <p class="text-muted mb-4">Register to continue.</p>

                <div class="text-center mb-3">
                    <?php 
                    if (!empty($errors)) { 
                        foreach ($errors as $error) { 
                            echo "<p class='text-danger'>" . htmlspecialchars($error) . "</p>"; 
                        } 
                    } 
                    if ($success !== "") { 
                        echo "<p class='text-success'>" . htmlspecialchars($success) . "</p>"; 
                    } 
                    ?>
                </div>

                <form method="POST" action="register.php">

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">NAME</label>
                        <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">EMAIL ADDRESS</label>
                        <input type="email" name="email" class="form-control" placeholder="uid@technikum-wien.at" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">PASSWORD</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">CONFIRM PASSWORD</label>
                        <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Register
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    Already have an account? <a href="login.php" class="accent-text">Sign in</a>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>