<!DOCTYPE html>
<html lang="en">
<?php
    include '../src/includes/header.php';

    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }

    $cid      = intval($_GET['cid'] ?? 0);
    $isMember = false;
    $isAdmin  = false;
    $groupName = '';
    $groupDesc = '';
    $members  = [];

    if ($cid > 0) {
        $stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
        $stmt->bindParam(':cid', $cid);
        $stmt->bindParam(':uid', $_SESSION['uid']);
        $stmt->execute();
        $roleRow  = $stmt->fetch(PDO::FETCH_ASSOC);
        $isMember = (bool) $roleRow;
        $isAdmin  = ($roleRow && $roleRow['role'] === 'admin');

        $stmt = $con->prepare("SELECT name, description FROM chat WHERE id = :cid");
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        $group     = $stmt->fetch(PDO::FETCH_ASSOC);
        $groupName = htmlspecialchars($group['name'] ?? 'Group Chat');
        $groupDesc = htmlspecialchars($group['description'] ?? '');

        $stmt = $con->prepare("
            SELECT u.id, u.name, cu.role
            FROM users u
            JOIN chat_user cu ON cu.user_id = u.id
            WHERE cu.chat_id = :cid
            ORDER BY cu.role DESC, u.name ASC
        ");
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        $members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>
<script src="assets/js/chat.js" defer></script>

<body class="bg-light">

<div class="container py-3">

    <div class="dropdown mb-3">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            About
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="group_files.php?cid=<?=$cid?>">View Files</a></li>
            <li><a class="dropdown-item" href="invite.php?cid=<?=$cid?>">Invite Members</a></li>
            <li><a class="dropdown-item" href="file_upload.php?cid=<?=$cid?>">Upload File</a></li>
            <?php if ($isAdmin): ?>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="edit_group.php?cid=<?=$cid?>">Edit Group</a></li>
            <?php endif; ?>
            <?php if ($isMember): ?>
            <li><hr class="dropdown-divider"></li>
            <li>
                <button class="dropdown-item text-danger" type="button"
                        onclick="if(confirm('Are you sure you want to leave this group?')) document.getElementById('leaveForm').submit()">
                    Leave Group
                </button>
            </li>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
            <li>
                <button class="dropdown-item text-danger" type="button"
                        onclick="if(confirm('Permanently delete this group? This cannot be undone.')) document.getElementById('deleteForm').submit()">
                    Delete Group
                </button>
            </li>
            <?php endif; ?>
        </ul>
    </div>

    <?php if ($isMember): ?>
    <form id="leaveForm" method="POST" action="../src/auth/leave_group.php">
        <input type="hidden" name="cid" value="<?=$cid?>">
    </form>
    <?php endif; ?>
    <?php if ($isAdmin): ?>
    <form id="deleteForm" method="POST" action="../src/auth/delete_group.php">
        <input type="hidden" name="cid" value="<?=$cid?>">
    </form>
    <?php endif; ?>

    <div class="chat-panel shadow-sm mb-3">
        <div class="chat-panel-body py-2 px-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0">💬 <?= $groupName ?></h5>
            <button class="btn btn-sm btn-outline-secondary" type="button"
                    data-bs-toggle="collapse" data-bs-target="#memberPanel" aria-expanded="false">
                👥 <?= count($members) ?> members
            </button>
        </div>
        <div class="collapse" id="memberPanel">
            <?php if ($groupDesc): ?>
            <div class="px-3 py-2 border-top text-muted small fst-italic"><?= $groupDesc ?></div>
            <?php endif; ?>
            <ul class="list-group list-group-flush border-top">
                <?php foreach ($members as $m): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                    <span><?= htmlspecialchars($m['name']) ?></span>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($m['role'] === 'admin'): ?>
                        <span class="badge bg-warning text-dark">admin</span>
                        <?php endif; ?>
                        <?php if ($isAdmin && $m['id'] != $_SESSION['uid']): ?>
                        <button class="btn btn-sm btn-outline-secondary py-0 px-2"
                                onclick="manageMember(<?=$cid?>, <?=$m['id']?>, '<?= $m['role'] === 'admin' ? 'demote' : 'promote' ?>')">
                            <?= $m['role'] === 'admin' ? 'Remove Admin' : 'Make Admin' ?>
                        </button>
                        <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                onclick="manageMember(<?=$cid?>, <?=$m['id']?>, 'kick')">
                            Kick
                        </button>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="chat-panel shadow-sm">
        <div class="chat-panel-body p-0">

            <div id="messageContainer" class="chat-box p-3 overflow-auto"></div>

            <form id="chatForm" class="border-top p-2 d-flex gap-2 bg-white">
                <input
                    type="text"
                    id="message"
                    class="form-control"
                    placeholder="Type a message..."
                    autocomplete="off"
                >
                <button class="btn btn-primary px-4" type="submit">Send</button>
                <input type="hidden" id="chatId" value="<?=$cid?>">
                <input type="hidden" id="uid_reference" value="<?=$_SESSION['uid']?>">
                <input type="hidden" id="loadCount" value="1">
            </form>

        </div>
    </div>

</div>

</body>
</html>
