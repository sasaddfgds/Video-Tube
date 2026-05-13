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
$parent_id = isset($data['parent_id']) ? (int)$data['parent_id'] : null;
$text = isset($data['text']) ? trim($data['text']) : '';
$user_id = $_SESSION['user_id'];

if ($video_id > 0 && !empty($text)) {
    try {
        $stmt = $db->prepare("INSERT INTO comments (video_id, user_id, parent_id, text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$video_id, $user_id, $parent_id, $text]);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'invalid_data']);
}
?>