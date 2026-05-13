<?php
require '../includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$comment_id = isset($data['comment_id']) ? (int)$data['comment_id'] : 0;
$type = isset($data['type']) ? $data['type'] : ''; // 'like' or 'dislike'
$user_id = $_SESSION['user_id'];

if ($comment_id > 0 && ($type === 'like' || $type === 'dislike')) {
    try {
        $stmtCheck = $db->prepare("SELECT type FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $stmtCheck->execute([$comment_id, $user_id]);
        $currentVote = $stmtCheck->fetchColumn();

        if ($currentVote === $type) {
            $db->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?")->execute([$comment_id, $user_id]);
            $action = 'removed';
        } else {
            $db->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?")->execute([$comment_id, $user_id]);
            $db->prepare("INSERT INTO comment_likes (comment_id, user_id, type) VALUES (?, ?, ?)")->execute([$comment_id, $user_id, $type]);
            $action = 'voted';
        }

        $likes = $db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND type = 'like'")->execute([$comment_id]) ? $db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND type = 'like'")->fetchColumn() : 0; // Fixed below
        
        // Correct way to get counts
        $stmtL = $db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND type = 'like'");
        $stmtL->execute([$comment_id]);
        $likesCount = $stmtL->fetchColumn();

        $stmtD = $db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND type = 'dislike'");
        $stmtD->execute([$comment_id]);
        $dislikesCount = $stmtD->fetchColumn();

        echo json_encode([
            'action' => $action, 
            'likes' => $likesCount,
            'dislikes' => $dislikesCount,
            'type' => $type
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'invalid_data']);
}
?>