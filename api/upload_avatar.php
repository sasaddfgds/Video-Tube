<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $avatar = $_FILES['avatar'];
    if ($avatar['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($avatar['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $avatarName = uniqid() . '.' . $ext;
            $avatarPath = '../uploads/avatars/' . $avatarName;

            if (move_uploaded_file($avatar['tmp_name'], $avatarPath)) {
                $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$avatarName, $_SESSION['user_id']]);
            }
        }
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
