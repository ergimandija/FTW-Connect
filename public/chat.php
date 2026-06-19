<!DOCTYPE html>
<html lang="en">

<?php
    include '../src/auth/auth_check.php';


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
            SELECT u.id, u.name, cu.role, cu.nickname
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
    <form id="leaveForm" method="POST" action="../src/groups/leave_group.php">
        <input type="hidden" name="cid" value="<?=$cid?>">
    </form>
    <?php endif; ?>
    <?php if ($isAdmin): ?>
    <form id="deleteForm" method="POST" action="../src/groups/delete_group.php">
        <input type="hidden" name="cid" value="<?=$cid?>">
    </form>
    <?php endif; ?>
    <!-- Header -->
    <div class="chat-panel shadow-sm mb-3">
        <div class="chat-panel-body py-2 px-3">
            <h5 class="mb-0">💬 Chat</h5>
        </div>
    </div>

    <!-- Chat box -->
    <div class="chat-panel shadow-sm">
        <div class="chat-panel-body p-0">

            <div id="messageContainer" class="chat-box p-3 overflow-auto"></div>

            <!-- Input -->
            <form id="chatForm" class="border-top p-2 d-flex gap-2 bg-white">
                <input 
                    type="text" 
                    id="message" 
                    class="form-control" 
                    placeholder="Type a message..."
                    autocomplete="off"
                >
                <input 
                    type="file"
                    id="fileInput"
                     accept=".jpg,.jpeg,.png,.gif,.webp"
                    hidden/>
                <label for="fileInput" class="btn btn-primary" id="attachPicture">
                    📎
                </label>
                <button class="btn btn-primary px-4" type="submit">
                    Send
                </button>

                <input type="hidden" id="chatId" value="<?= $_GET['cid']; ?>">
                <input type="hidden" id="uid_reference" value="<?= $_SESSION['uid'] ?>">
                <input type="hidden" id="loadCount" value="1">
            </form>

        </div>
    </div>

</div>

</body>
</html>