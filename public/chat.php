<!DOCTYPE html>
<html lang="en">
<?php
    include '../src/includes/header.php';

    if (empty($_SESSION['uid'])) {
        header('Location: login.php');
        exit;
    }

    $cid = intval($_GET['cid'] ?? 0);
    $isMember = false;
    $isAdmin = false;

    if ($cid > 0 && !empty($_SESSION['uid'])) {
        $stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
        $stmt->bindParam(':cid', $cid);
        $stmt->bindParam(':uid', $_SESSION['uid']);
        $stmt->execute();
        $roleRow = $stmt->fetch(PDO::FETCH_ASSOC);
        $isMember = (bool) $roleRow;
        $isAdmin  = ($roleRow && $roleRow['role'] === 'admin');
    }
?>
   <script src="assets/js/chat.js" defer></script>
<body>
    <div class="mb-3 ms-3 mt-3">
        <a href="group_files.php?cid=<?=$cid;?>" class="btn btn-info">View Group Files</a>
        <a href="invite.php?cid=<?=$cid;?>" class="btn btn-outline-primary">Invite Members</a>
        <a href="file_upload.php?cid=<?=$cid;?>" class="btn btn-outline-secondary">Upload File</a>
        <?php if ($isMember): ?>
        <a href="edit_group.php?cid=<?=$cid;?>" class="btn btn-outline-warning">Edit Group</a>
        <?php endif; ?>
        <?php if ($isMember): ?>
        <form method="POST" action="../src/auth/leave_group.php" class="d-inline"
              onsubmit="return confirm('Are you sure you want to leave this group?');">
            <input type="hidden" name="cid" value="<?=$cid;?>">
            <button type="submit" class="btn btn-outline-danger">Leave Group</button>
        </form>
        <?php endif; ?>
        <?php if ($isAdmin): ?>
        <form method="POST" action="../src/auth/delete_group.php" class="d-inline"
              onsubmit="return confirm('Are you sure you want to permanently delete this group? This cannot be undone.');">
            <input type="hidden" name="cid" value="<?=$cid;?>">
            <button type="submit" class="btn btn-danger">Delete Group</button>
        </form>
        <?php endif; ?>
    </div>
    <h1>Chat </h1>
    <div style="padding:2px; margin: 10px; height: 200px;" class="overflow-auto" id="messageContainer">

    </div>
    <form id="chatForm" >
        <input type="text" id="message" placeholder="send message">
        <input type="hidden" id="chatId" value="<?=$cid;?>">
        <input type="hidden" id="uid_reference" value="<?=$_SESSION['uid']?>">
        <input type="hidden" id="loadCount" value="1">
        <input type="submit">
    </form>
</body>
</html>
