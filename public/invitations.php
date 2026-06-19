<!DOCTYPE html>
<html lang="en">
<?php include '../src/includes/header.php'; ?>
<body>
    <?php
    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }
    ?>

    <div class="container mt-4">
        <div class="mb-3">
            <a href="userchats.php" class="btn btn-secondary">Back to chats</a>
        </div>

        <h2 class="mb-4">Pending Invitations</h2>

        <div id="invitations-container">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <script>
        async function loadInvitations() {
            try {
                const response = await fetch('../src/api/getInvitations.php');
                const invitations = await response.json();

                if (!Array.isArray(invitations)) {
                    document.getElementById('invitations-container').innerHTML = '<div class="alert alert-danger">Error loading invitations</div>';
                    return;
                }

                if (invitations.length === 0) {
                    document.getElementById('invitations-container').innerHTML = '<p class="text-muted text-center">No pending invitations.</p>';
                    return;
                }

                let html = '';
                invitations.forEach(inv => {
                    const picture = inv.chat_picture || 'assets/img/anonymous.png';
                    const createdAt = new Date(inv.created_at).toLocaleDateString();

                    html += `
                        <div class="card mb-3" id="invitation-${inv.id}">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <img src="${escapeHtml(picture)}" alt="Group" style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 15px;">
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1">${escapeHtml(inv.chat_name)}</h5>
                                        <small class="text-muted">Invited by ${escapeHtml(inv.invited_by_name)} on ${createdAt}</small>
                                    </div>
                                    <div id="buttons-${inv.id}">
                                        <button class="btn btn-sm btn-success me-2 accept-btn" data-invitation-id="${inv.id}">Accept</button>
                                        <button class="btn btn-sm btn-outline-danger decline-btn" data-invitation-id="${inv.id}">Decline</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                document.getElementById('invitations-container').innerHTML = html;

                document.querySelectorAll('.accept-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        respondInvitation(this.dataset.invitationId, 'accept');
                    });
                });

                document.querySelectorAll('.decline-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        respondInvitation(this.dataset.invitationId, 'decline');
                    });
                });

            } catch (error) {
                document.getElementById('invitations-container').innerHTML = '<div class="alert alert-danger">Error: ' + error.message + '</div>';
            }
        }

        async function respondInvitation(invitationId, action) {
            try {
                const response = await fetch('../src/api/respondInvitation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        invitation_id: parseInt(invitationId),
                        action: action
                    })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    const buttonsDiv = document.getElementById(`buttons-${invitationId}`);
                    const badge = action === 'accept'
                        ? '<span class="badge bg-success">✓ Accepted</span>'
                        : '<span class="badge bg-danger">✗ Declined</span>';
                    buttonsDiv.innerHTML = badge;
                } else {
                    alert('Error: ' + (result.message || 'Failed to respond to invitation'));
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

        document.addEventListener('DOMContentLoaded', loadInvitations);
    </script>
</body>
</html>
