<?php
require 'includes/db.php';
require 'includes/header.php';

$stmt = $db->query("
    SELECT videos.*, users.username, 
    (SELECT COUNT(*) FROM likes WHERE video_id = videos.id) as like_count
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    ORDER BY like_count DESC, videos.created_at DESC
    LIMIT 20
");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h2>🔥 Trendy</h2>
    <p>Najpopularniejsze filmy na Video-Tube</p>
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
                        <div class="video-meta">
                            <span class="video-date"><?= date('d.m.Y', strtotime($video['created_at'])) ?></span>
                            <span class="video-likes">❤️ <?= $video['like_count'] ?></span>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <p>Brak filmów do wyświetlenia.</p>
        </div>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>