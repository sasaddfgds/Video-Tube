<?php
require 'includes/db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $avatar = $_FILES['avatar'];
    if ($avatar['error'] === 0) {
        $ext = pathinfo($avatar['name'], PATHINFO_EXTENSION);
        $avatarName = uniqid() . '.' . $ext;
        $avatarPath = 'uploads/avatars/' . $avatarName;

        if (move_uploaded_file($avatar['tmp_name'], $avatarPath)) {
            $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            if ($stmt->execute([$avatarName, $_SESSION['user_id']])) {
                $success = "Awatar został zaktualizowany!";
            } else {
                $error = "Błąd bazy danych.";
            }
        } else {
            $error = "Błąd przesyłania pliku.";
        }
    }
}

$stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$userAvatar = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM videos WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$videoCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM comments WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$commentCount = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM likes WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$likeCount = $stmt->fetchColumn();
?>

<div class="profile-container">
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <div class="profile-header">
        <div class="profile-avatar-large">
            <?php if ($userAvatar): ?>
                <img src="uploads/avatars/<?= htmlspecialchars($userAvatar) ?>" alt="Avatar" class="avatar-img">
            <?php else: ?>
                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
            <?php endif; ?>
            
            <form id="avatarForm" method="POST" enctype="multipart/form-data" class="avatar-upload-overlay">
                <label for="avatarInput" class="avatar-label">
                    <span class="camera-icon">📷</span>
                </label>
                <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
            </form>
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($_SESSION['username']) ?></h1>
            <p>Użytkownik Video-Tube</p>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <span class="stat-value"><?= $videoCount ?></span>
            <span class="stat-label">Filmy</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?= $commentCount ?></span>
            <span class="stat-label">Komentarze</span>
        </div>
        <div class="stat-card">
            <span class="stat-value"><?= $likeCount ?></span>
            <span class="stat-label">Polubienia</span>
        </div>
    </div>

    <div class="profile-actions">
        <a href="my_videos.php" class="btn">Zarządzaj filmami</a>
        <a href="upload.php" class="btn primary">Prześlij nowy film</a>
        <a href="logout.php" class="btn logout-btn">Wyloguj się</a>
    </div>
</div>

<?php require 'includes/footer.php'; ?>