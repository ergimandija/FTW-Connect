<!DOCTYPE html>
<html lang="en">
<?php
include '../src/includes/header.php';

if (empty($_SESSION['uid'])) {
    header('Location: ../public/index.php');
    exit;
}

$cid = intval($_GET['cid'] ?? 0);

if ($cid <= 0) {
    die('Invalid group ID');
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();

$adminCheck = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$adminCheck || $adminCheck['role'] !== 'admin') {
    $accessDenied = true;
} else {
    $accessDenied = false;

    $stmt = $con->prepare("SELECT name FROM chat WHERE id = :id");
    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $groupData = $stmt->fetch(PDO::FETCH_ASSOC);
    $groupName = $groupData['name'] ?? 'Group';
}
?>
<body>
    <div class="container mt-4">
        <?php if ($accessDenied): ?>
            <div class="alert alert-danger">
                You do not have permission to invite members to this group.
            </div>
            <a href="chat.php?cid=<?=$cid;?>" class="btn btn-secondary">Back to Chat</a>
        <?php else: ?>
            <div class="mb-3">
                <a href="chat.php?cid=<?=$cid;?>" class="btn btn-secondary">Back to Chat</a>
            </div>

            <h2>Invite Members to <?=htmlspecialchars($groupName);?></h2>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="input-group">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name or email">
                        <button class="btn btn-primary" id="searchBtn">Search</button>
                    </div>
                </div>
            </div>

            <div id="resultsContainer">
                <p class="text-muted">Enter a search term to find users to invite.</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const chatId = <?=$cid;?>;

        async function searchUsers() {
            const q = document.getElementById('searchInput').value.trim();

            if (!q) {
                document.getElementById('resultsContainer').innerHTML = '<p class="text-muted">Enter a search term to find users to invite.</p>';
                return;
            }

            try {
                const response = await fetch(`../src/api/searchUsers.php?q=${encodeURIComponent(q)}&cid=${chatId}`);
                const results = await response.json();

                if (!Array.isArray(results)) {
                    document.getElementById('resultsContainer').innerHTML = '<p class="text-danger">Error: ' + (results.message || 'Unknown error') + '</p>';
                    return;
                }

                if (results.length === 0) {
                    document.getElementById('resultsContainer').innerHTML = '<p class="text-muted">No users found.</p>';
                    return;
                }

                let html = '<div class="list-group">';

                results.forEach(user => {
                    const profilePic = user.profilePicturePath ? user.profilePicturePath : 'assets/img/anonymous.png';
                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="${escapeHtml(profilePic)}" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 10px;">
                                <div>
                                    <strong>${escapeHtml(user.name)}</strong>
                                    <br>
                                    <small class="text-muted">${escapeHtml(user.email)}</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-success invite-btn" data-user-id="${user.id}" data-user-name="${user.name}">Invite</button>
                        </div>
                    `;
                });

                html += '</div>';
                document.getElementById('resultsContainer').innerHTML = html;

                document.querySelectorAll('.invite-btn').forEach(btn => {
                    btn.addEventListener('click', async function() {
                        await inviteUser(this.dataset.userId, this.dataset.userName);
                    });
                });

            } catch (error) {
                document.getElementById('resultsContainer').innerHTML = '<p class="text-danger">Error: ' + error.message + '</p>';
            }
        }

        async function inviteUser(userId, userName) {
            const btn = document.querySelector(`button[data-user-id="${userId}"]`);
            const originalText = btn.textContent;

            try {
                const response = await fetch('../src/api/inviteUser.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        chat_id: chatId,
                        user_id: parseInt(userId)
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    btn.textContent = 'Invited ✓';
                    btn.disabled = true;
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                } else {
                    alert('Error: ' + (result.message || 'Failed to invite user'));
                }
            } catch (error) {
                alert('Error: ' + error.message);
            }
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        document.getElementById('searchBtn').addEventListener('click', searchUsers);
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchUsers();
            }
        });
    </script>
</body>
</html>
