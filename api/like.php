<?php
require '../includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$video_id = isset($data['video_id']) ? (int)$data['video_id'] : 0;
$user_id = $_SESSION['user_id'];

if ($video_id > 0) {
    try {
        $stmtCheck = $db->prepare("SELECT 1 FROM likes WHERE video_id = ? AND user_id = ?");
        $stmtCheck->execute([$video_id, $user_id]);
        $alreadyLiked = (bool)$stmtCheck->fetchColumn();

        if ($alreadyLiked) {
            $db->prepare("DELETE FROM likes WHERE video_id = ? AND user_id = ?")->execute([$video_id, $user_id]);
            $action = 'unliked';
        } else {
            $db->prepare("DELETE FROM dislikes WHERE video_id = ? AND user_id = ?")->execute([$video_id, $user_id]);
            $db->prepare("INSERT INTO likes (video_id, user_id) VALUES (?, ?)")->execute([$video_id, $user_id]);
            $action = 'liked';
        }

        $likes = $db->prepare("SELECT COUNT(*) FROM likes WHERE video_id = ?");
        $likes->execute([$video_id]);
        $likesCount = $likes->fetchColumn();

        $dislikes = $db->prepare("SELECT COUNT(*) FROM dislikes WHERE video_id = ?");
        $dislikes->execute([$video_id]);
        $dislikesCount = $dislikes->fetchColumn();

        echo json_encode([
            'action' => $action, 
            'likes' => $likesCount,
            'dislikes' => $dislikesCount
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'invalid_id']);
}
?>