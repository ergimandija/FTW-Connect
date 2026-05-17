<?php
include __DIR__ . '/../../config/config.php';

if (empty($_SESSION['uid'])) {
    header('Location: ../../public/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/userchats.php');
    exit;
}

$cid = intval($_POST['cid'] ?? 0);

if ($cid <= 0) {
    header('Location: ../../public/userchats.php');
    exit;
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $_SESSION['uid']);
$stmt->execute();
$roleRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$roleRow || $roleRow['role'] !== 'admin') {
    header('Location: ../../public/chat.php?cid=' . $cid);
    exit;
}

$stmt = $con->prepare("SELECT picture FROM chat WHERE id = :id");
$stmt->bindParam(':id', $cid);
$stmt->execute();
$group = $stmt->fetch(PDO::FETCH_ASSOC);

$con->beginTransaction();
try {
    $stmt = $con->prepare("DELETE FROM chat WHERE id = :id");
    $stmt->bindParam(':id', $cid);
    $stmt->execute();
    $con->commit();
} catch (PDOException $e) {
    $con->rollBack();
    header('Location: ../../public/chat.php?cid=' . $cid);
    exit;
}

// Delete files only after DB commit succeeds
if ($group && $group['picture']) {
    $picPath = __DIR__ . '/../../public/' . $group['picture'];
    if (file_exists($picPath)) {
        unlink($picPath);
    }
}

$uploadsDir = __DIR__ . '/../../public/assets/uploads/' . $cid;
if (is_dir($uploadsDir)) {
    foreach (glob($uploadsDir . '/*') as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir($uploadsDir);
}

header('Location: ../../public/userchats.php');
exit;
