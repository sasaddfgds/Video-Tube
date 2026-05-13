<?php
require 'includes/db.php';
require 'includes/header.php';

$userId = $_SESSION['user_id'] ?? 0;

if ($userId > 0) {
    $stmt = $db->prepare("
        SELECT videos.*, users.username 
        FROM videos 
        JOIN users ON videos.user_id = users.id 
        JOIN subscriptions ON subscriptions.creator_id = videos.user_id
        WHERE subscriptions.subscriber_id = ?
        ORDER BY videos.created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$userId]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $title = "Twoje subskrypcje";
    $subtitle = "Filmy od twórców, których subskrybujesz";
} else {
    $videos = [];
    $title = "Subskrypcje";
    $subtitle = "Zaloguj się, aby zobaczyć filmy ze swoich subskrypcji";
}
?>

<div class="page-header">
    <h2>📺 <?= $title ?></h2>
    <p><?= $subtitle ?></p>
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
            <?php if ($userId > 0): ?>
                <p>Nie subskrybujesz jeszcze żadnych twórców.</p>
                <a href="trending.php" class="btn primary">Odkryj popularne filmy</a>
            <?php else: ?>
                <p>Zaloguj się, aby zobaczyć swoje subskrypcje.</p>
                <a href="login.php" class="btn primary">Zaloguj się</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>