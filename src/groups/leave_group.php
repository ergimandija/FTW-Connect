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
$uid = $_SESSION['uid'];

if ($cid <= 0) {
    header('Location: ../../public/userchats.php');
    exit;
}

$stmt = $con->prepare("SELECT role FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
$stmt->bindParam(':cid', $cid);
$stmt->bindParam(':uid', $uid);
$stmt->execute();
$roleRow = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$roleRow) {
    header('Location: ../../public/userchats.php');
    exit;
}

$con->beginTransaction();
try {
    if ($roleRow['role'] === 'admin') {
        $stmt = $con->prepare("SELECT COUNT(*) FROM chat_user WHERE chat_id = :cid");
        $stmt->bindParam(':cid', $cid);
        $stmt->execute();
        $memberCount = (int) $stmt->fetchColumn();

        if ($memberCount === 1) {
            $stmt = $con->prepare("SELECT picture FROM chat WHERE id = :id");
            $stmt->bindParam(':id', $cid);
            $stmt->execute();
            $group = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $con->prepare("DELETE FROM chat WHERE id = :id");
            $stmt->bindParam(':id', $cid);
            $stmt->execute();

            $con->commit();

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
        }

        $stmt = $con->prepare("SELECT COUNT(*) FROM chat_user WHERE chat_id = :cid AND role = 'admin' AND user_id != :uid");
        $stmt->bindParam(':cid', $cid);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        $otherAdminCount = (int) $stmt->fetchColumn();

        if ($otherAdminCount === 0) {
            $stmt = $con->prepare("SELECT user_id FROM chat_user WHERE chat_id = :cid AND user_id != :uid ORDER BY user_id ASC LIMIT 1");
            $stmt->bindParam(':cid', $cid);
            $stmt->bindParam(':uid', $uid);
            $stmt->execute();
            $nextAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($nextAdmin) {
                $stmt = $con->prepare("UPDATE chat_user SET role = 'admin' WHERE chat_id = :cid AND user_id = :uid");
                $stmt->execute([':cid' => $cid, ':uid' => $nextAdmin['user_id']]);
            }
        }
    }

    $stmt = $con->prepare("DELETE FROM chat_user WHERE chat_id = :cid AND user_id = :uid");
    $stmt->bindParam(':cid', $cid);
    $stmt->bindParam(':uid', $uid);
    $stmt->execute();

    $con->commit();
} catch (PDOException $e) {
    $con->rollBack();
    header('Location: ../../public/chat.php?cid=' . $cid);
    exit;
}

header('Location: ../../public/userchats.php');
exit;
