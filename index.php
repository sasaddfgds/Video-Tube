<?php
require 'includes/db.php';
require 'includes/header.php';

$stmt = $db->query("
    SELECT videos.*, users.username 
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    ORDER BY videos.created_at DESC
");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <h2>Polecane</h2>
    <p>Filmy, które mogą Cię zainteresować</p>
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
        <p>Brak wideo do wyświetlenia. <a href="upload.php">Prześlij pierwsze wideo!</a></p>
    <?php endif; ?>
</div>

<?php require 'includes/footer.php'; ?>