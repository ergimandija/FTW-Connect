<?php
header('Content-Type: application/json');
include '../../config/config.php';
include '../includes/crypto.php';


if (isset($_GET['cid'])) {
    try {
        $stmt = $con->prepare('SELECT * FROM chat_user WHERE chat_id = :chid AND user_id = :uid');
        $stmt->bindParam(':chid', $_GET['cid']);
        $stmt->bindParam(':uid', $_SESSION['uid']);
        $stmt->execute();
        $check = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!empty($check['chat_id'])) {
            $stmt = $con->prepare('SELECT count(*) FROM message WHERE chat_id = :chid');
            $stmt->bindParam(':chid', $_GET['cid']);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            $loadCount = $_GET['loadCount'];
            $limit  = min((int)($loadCount * 50), (int)$count);
            $offset = max(0, $count - $limit);

            $stmt = $con->prepare("
                SELECT m.id, m.chat_id, m.sender_id, m.content, m.sent_at,
                       COALESCE(cu.nickname, u.name) AS sender_name
                FROM message m
                JOIN users u ON u.id = m.sender_id
                LEFT JOIN chat_user cu ON cu.chat_id = m.chat_id AND cu.user_id = m.sender_id
                WHERE m.chat_id = :chid
                ORDER BY m.sent_at ASC
                LIMIT " . $limit . " OFFSET " . $offset);
            $stmt->bindParam(':chid', $_GET['cid']);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($messages as &$msg) {
                $msg['content'] = Crypto::decrypt($msg['content']);
            }
            unset($msg);

            if (!empty($messages)) {
                $ids          = array_column($messages, 'id');
                $placeholders = implode(',', array_fill(0, count($ids), '?'));

                $stmt = $con->prepare("
                    SELECT message_id, emoji, COUNT(*) AS count,
                           MAX(CASE WHEN user_id = ? THEN 1 ELSE 0 END) AS user_reacted
                    FROM message_reaction
                    WHERE message_id IN ($placeholders)
                    GROUP BY message_id, emoji
                ");
                $stmt->execute(array_merge([$_SESSION['uid']], $ids));
                $rawReactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $reactionMap = [];
                foreach ($rawReactions as $r) {
                    $reactionMap[$r['message_id']][] = [
                        'emoji'        => $r['emoji'],
                        'count'        => (int) $r['count'],
                        'user_reacted' => (bool) $r['user_reacted'],
                    ];
                }
                foreach ($messages as &$msg) {
                    $msg['reactions'] = $reactionMap[$msg['id']] ?? [];
                }
            }

            echo json_encode([
                'messages' => $messages,
                'total'    => $count,
                'limit'    => $limit,
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'user is not in the chat']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
