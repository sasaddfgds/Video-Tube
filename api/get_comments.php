<?php
require '../includes/db.php';
session_start();

header('Content-Type: application/json');

$video_id = isset($_GET['video_id']) ? (int)$_GET['video_id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

if ($video_id > 0) {
    $stmt = $db->prepare("
        SELECT c.*, u.username, u.avatar,
        (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'like') as likes,
        (SELECT COUNT(*) FROM comment_likes WHERE comment_id = c.id AND type = 'dislike') as dislikes,
        (SELECT type FROM comment_likes WHERE comment_id = c.id AND user_id = ?) as user_vote
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.video_id = ?
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$user_id, $video_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $commentTree = [];
    $flatComments = [];
    
    foreach ($comments as $c) {
        $c['replies'] = [];
        $flatComments[$c['id']] = $c;
    }
    
    foreach ($flatComments as $id => &$c) {
        if ($c['parent_id'] && isset($flatComments[$c['parent_id']])) {
            $flatComments[$c['parent_id']]['replies'][] = &$c;
        } else {
            $commentTree[] = &$c;
        }
    }
    
    echo json_encode($commentTree);
} else {
    echo json_encode([]);
}
?>