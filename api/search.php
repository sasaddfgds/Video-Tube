<?php
require '../includes/db.php';

header('Content-Type: application/json');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) > 0) {
    $stmt = $db->prepare("
        SELECT videos.id, videos.title, users.username 
        FROM videos 
        JOIN users ON videos.user_id = users.id 
        WHERE videos.title LIKE ? 
        LIMIT 5
    ");
    $stmt->execute(['%' . $query . '%']);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]);
}
?>