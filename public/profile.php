<?php
include '../src/includes/header.php'; 

include '../src/auth/auth_check.php';

$loggedInUserId = $_SESSION['uid'];

$userId = isset($_GET['id']) ? (int) $_GET['id'] : $loggedInUserId;

$isOwnProfile = ($userId === $loggedInUserId);

try {
    $stmt = $con->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    if(!$user) {
        header("Location: ../../public/profile.php?error=User not found");
        exit;
    }

    $postStmt = $con->prepare("SELECT * FROM post WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $postStmt->execute([$userId]);
    $posts = $postStmt->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$message = "";
$messageType = "";

if (isset($_GET['success'])) {
    $message = "Profile updated successfully!";
    $messageType = "success";
} elseif (isset($_GET['error'])) {
    $message = "Error: " . htmlspecialchars($_GET['error']);
    $messageType = "danger";
}

function formatLastSeen($timestamp) {
    if (empty($timestamp)) {
        return "Never";
    }

    $tz = new DateTimeZone('Europe/Vienna');

    try {
        $lastSeen = new DateTime($timestamp, $tz);
    } catch (Exception $e) {
        return "Never";
    }

    $now = new DateTime('now', $tz);
    $diff = $now->getTimestamp() - $lastSeen->getTimestamp();

    if ($diff < 0) {
        $diff = 0;
    }


    // If active in the last 3 minutes, consider them online
    if ($diff < 180) {
        return '<span class="badge bg-success">Online</span>';
    }

    $minutes = floor($diff / 60);
    if ($minutes < 60) {
        return "Last seen " . $minutes . "m ago";
    }

    $hours = floor($diff / 3600);
    if ($hours < 24) {
        return "Last seen " . $hours . "h ago";
    }

    $days = floor($diff / 86400);
    if ($days < 7) {
        return "Last seen " . $days . "d ago";
    }

    return "Last seen on " . $lastSeen->format('M d, Y');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | FTW Connect</title>
    <link href="assets/styles/profile.css" rel="stylesheet">
</head>
<body>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
    <div id="feedbackToast" class="toast align-items-center text-white bg-<?= $messageType ?> border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi <?= $messageType === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?> me-2"></i>
                <?= $message ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="container">
    <?php include '../src/includes/search_bar.php'; ?>
    <div class="row">
        
        <div class="col-lg-4">
            <div class="card card-profile mb-4">
                <div class="profile-header-bg"></div>
                <div class="profile-img-container">
                    <img src="<?= $user['profilePicturePath'] ?? 'assets/img/anonymous.png' ?>" class="rounded-circle" alt="Avatar">
                </div>
                <div class="card-body text-center">
                    <h4 class="mb-1"><?= htmlspecialchars($user['name']) ?></h4>
                    <p class="text-muted small mb-3"><?= htmlspecialchars($user['email']) ?></p>
                    
                    <?php if (!$isOwnProfile): ?>
                        <div class="mb-3 small">
                            <i class="bi bi-eye text-muted me-1"></i> 
                            <?= formatLastSeen($user['last_seen'] ?? '') ?>
                        </div>
                    <?php endif; ?>

                    <?php if($user['status']): ?>
                        <div class="mb-3">
                            <span class="status-badge">
                                <i class="bi bi-chat-dots me-1"></i> <?= htmlspecialchars($user['status']) ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    
                    <hr>

                    <?php if($isOwnProfile): ?>
                        <div class="d-grid gap-2">
                            <button class="btn btn-ftw" data-bs-toggle="modal" data-bs-target="#editModal">
                                <i class="bi bi-gear-fill me-2"></i>Edit Profile
                            </button>

                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="bi bi-trash-fill me-2"></i>Delete Account
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card card-profile p-3">
                <h6 class="fw-bold mb-3">Statistics</h6>
                <div class="d-flex justify-content-between small text-muted">
                    <span>Member since:</span>
                    <span><?= date('M d, Y', strtotime($user['created_at'])) ?></span>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <?php if($isOwnProfile): ?>
            <div class="post-card p-4">
                <h5 class="mb-3">Create a Post</h5>
                <form action="backend/create_post.php" method="POST">
                    <input type="text" name="title" class="form-control mb-2" placeholder="Post title" required>
                    <textarea name="content" class="form-control mb-3" rows="3" placeholder="Share your thoughts..."></textarea>
                    <div class="text-end">
                        <button type="submit" class="btn btn-outline-ftw">Post</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <h5 class="mb-4">
                <?= $isOwnProfile ? 'Your Recent Posts' : htmlspecialchars($user['name']) . '\'s Posts' ?>
            </h5>

            <?php if(empty($posts)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-chat-left-text display-1 text-light"></i>
                    <p class="text-muted mt-3">No posts yet.</p>
                </div>
            <?php else: ?>
                <?php foreach($posts as $post): ?>
                    <div class="post-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0 text-primary"><?= htmlspecialchars($post['title']) ?></h6>
                            <small class="text-muted"><?= date('H:i, M d, Y', strtotime($post['created_at'])) ?></small>
                        </div>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                        <?php if($post['image_url']): ?>
                            <img src="<?= $post['image_url'] ?>" class="img-fluid rounded mt-3">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($isOwnProfile): ?>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="../src/api/update_profile.php" enctype="multipart/form-data">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Update Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">NAME</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">EMAIL</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">NEW PASSWORD (OPTIONAL)</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave empty to keep current password">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">CHANGE PROFILE PICTURE</label>
                    <input type="file" name="profile_pic" class="form-control" accept="image/*">
                    <div class="form-text">Recommended: Square, max 2MB.</div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-ftw">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="../src/api/delete_account.php">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Delete Account
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-3">
                    Are you sure you want to permanently delete your account?
                </p>

                <div class="alert alert-danger mb-0">
                    <strong>Warning:</strong> This action cannot be undone.
                    All your posts and profile data will be deleted permanently.
                </div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-trash-fill me-2"></i>
                    Delete Permanently
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if ($message !== ""): ?>
            const toastElement = document.getElementById('feedbackToast');
            const toast = new bootstrap.Toast(toastElement, {
            });
            toast.show();
        <?php endif; ?>
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>