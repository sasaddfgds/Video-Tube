<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'not_logged_in']);
    exit;
}

$subscriber_id = $_SESSION['user_id'];
$creator_id = isset($_POST['creator_id']) ? (int)$_POST['creator_id'] : 0;

if ($creator_id === 0 || $subscriber_id === $creator_id) {
    echo json_encode(['error' => 'invalid_request']);
    exit;
}

try {
    $stmt = $db->prepare("SELECT 1 FROM subscriptions WHERE subscriber_id = ? AND creator_id = ?");
    $stmt->execute([$subscriber_id, $creator_id]);
    
    if ($stmt->fetch()) {
        $stmt = $db->prepare("DELETE FROM subscriptions WHERE subscriber_id = ? AND creator_id = ?");
        $stmt->execute([$subscriber_id, $creator_id]);
        echo json_encode(['status' => 'unsubscribed']);
    } else {
        $stmt = $db->prepare("INSERT INTO subscriptions (subscriber_id, creator_id) VALUES (?, ?)");
        $stmt->execute([$subscriber_id, $creator_id]);
        echo json_encode(['status' => 'subscribed']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
