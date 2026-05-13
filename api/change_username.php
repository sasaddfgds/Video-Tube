<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Zaloguj się']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$newUsername = isset($data['username']) ? trim($data['username']) : '';

if (empty($newUsername)) {
    echo json_encode(['success' => false, 'message' => 'Nazwa nie może być pusta']);
    exit;
}

if (strlen($newUsername) < 3) {
    echo json_encode(['success' => false, 'message' => 'Nazwa jest za krótka']);
    exit;
}

$stmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$stmt->execute([$newUsername, $_SESSION['user_id']]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Ta nazwa jest już zajęta']);
    exit;
}

$stmt = $db->prepare("UPDATE users SET username = ? WHERE id = ?");
if ($stmt->execute([$newUsername, $_SESSION['user_id']])) {
    $_SESSION['username'] = $newUsername;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Błąd bazy danych']);
}
