<?php
require '../includes/db.php';

$ids_str = $_GET['ids'] ?? '';
if (empty($ids_str)) {
    echo json_encode([]);
    exit;
}

$ids = explode(',', $ids_str);
$ids = array_filter($ids, 'is_numeric');

if (empty($ids)) {
    echo json_encode([]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $db->prepare("
    SELECT videos.*, users.username 
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    WHERE videos.id IN ($placeholders)
");
$stmt->execute($ids);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sorted_videos = [];
foreach ($ids as $id) {
    foreach ($videos as $video) {
        if ($video['id'] == $id) {
            $sorted_videos[] = $video;
            break;
        }
    }
}

header('Content-Type: application/json');
echo json_encode($sorted_videos);