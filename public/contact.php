<!DOCTYPE html>
<?php require_once __DIR__ . '/../config/config.php'; ?>
<?php require_once __DIR__ . '/../src/contact/contact.php'; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | FTW Connect</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/styles/login.css">
</head>

<body>
<div class="container-fluid h-100">
    <div class="row h-100">

        <div class="col-md-6 d-none d-md-flex align-items-center justify-content-center left-side m-0 p-0">
            <div class="logo-box w-100 h-100 p-0">
                <img src="assets/img/FH_Technikum_Wien.jpg" alt="FTW Connect" class="w-100 h-100 object-fit-cover">
            </div>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div style="width: 100%; max-width: 420px; padding: 20px;">

                <div class="text-center d-md-none mb-4">
                    <img src="assets/img/Logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 300px;">
                </div>

                <h3 class="mb-2">Contact Us</h3>
                <p class="text-muted mb-4">Have a question or feedback? We'd love to hear from you.</p>

                <div class="text-center mb-3">
                    <?php if (!empty($errors)): ?>
                        <?php foreach ($errors as $error): ?>
                            <p class="text-danger"><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($success !== ""): ?>
                        <p class="text-success"><?= htmlspecialchars($success) ?></p>
                    <?php endif; ?>
                </div>

                <form method="POST" action="contact.php">

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">YOUR NAME</label>
                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            placeholder="John Doe"
                            maxlength="255"
                            required
                            value="<?= htmlspecialchars($name) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">EMAIL ADDRESS</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            placeholder="uid@technikum-wien.at"
                            maxlength="255"
                            required
                            value="<?= htmlspecialchars($email) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">SUBJECT</label>
                        <input
                            type="text"
                            name="subject"
                            class="form-control"
                            placeholder="What's this about?"
                            maxlength="255"
                            required
                            value="<?= htmlspecialchars($subject) ?>"
                        >
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary small fw-bold">MESSAGE</label>
                        <textarea
                            name="message"
                            class="form-control"
                            placeholder="Write your message here..."
                            rows="4"
                            style="resize: none;"
                            required
                        ><?= htmlspecialchars($message) ?></textarea>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn login-btn text-white">
                            Send Message
                        </button>
                    </div>
                </form>

                <p class="mt-4 text-muted text-center">
                    <a href="index.php" class="accent-text">Back to the homepage</a>
                </p>

            </div>
        </div>

    </div>
</div>
</body>
</html>
