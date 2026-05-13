<?php
require 'includes/db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $db->prepare("
    SELECT videos.*, users.username 
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    WHERE videos.user_id = ?
    ORDER BY videos.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h2>Moje filmy</h2>
    <p>Zarządzaj swoimi przesłanymi filmami</p>
</div>

<div class="video-grid">
    <?php if (count($videos) > 0): ?>
        <?php foreach ($videos as $video): ?>
            <div class="video-card">
                <a href="video.php?id=<?= $video['id'] ?>">
                    <img src="uploads/posters/<?= htmlspecialchars($video['poster']) ?>" alt="Poster" class="video-thumbnail">
                    <div class="video-info">
                        <h3><?= htmlspecialchars($video['title']) ?></h3>
                        <p class="video-author"><?= htmlspecialchars($video['username']) ?></p>
                        <p class="video-date"><?= date('d.m.Y', strtotime($video['created_at'])) ?></p>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Nie przesłałeś jeszcze żadnych filmów.</p>
            <a href="upload.php" class="btn primary">Prześlij swój pierwszy film</a>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>