<?php
require 'includes/db.php';
require 'includes/header.php';

$video_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $db->prepare("
    SELECT videos.*, users.username, users.avatar as user_avatar 
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    WHERE videos.id = ?
");
$stmt->execute([$video_id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    echo "<p>Nie znaleziono wideo.</p>";
    require 'includes/footer.php';
    exit;
}

$stmtLikes = $db->prepare("SELECT COUNT(*) FROM likes WHERE video_id = ?");
$stmtLikes->execute([$video_id]);
$likesCount = $stmtLikes->fetchColumn();

$stmtDislikes = $db->prepare("SELECT COUNT(*) FROM dislikes WHERE video_id = ?");
$stmtDislikes->execute([$video_id]);
$dislikesCount = $stmtDislikes->fetchColumn();

$userLiked = false;
$userDisliked = false;
if (isset($_SESSION['user_id'])) {
    $stmtUserLike = $db->prepare("SELECT 1 FROM likes WHERE video_id = ? AND user_id = ?");
    $stmtUserLike->execute([$video_id, $_SESSION['user_id']]);
    $userLiked = (bool)$stmtUserLike->fetchColumn();

    $stmtUserDislike = $db->prepare("SELECT 1 FROM dislikes WHERE video_id = ? AND user_id = ?");
    $stmtUserDislike->execute([$video_id, $_SESSION['user_id']]);
    $userDisliked = (bool)$stmtUserDislike->fetchColumn();

    $stmtSub = $db->prepare("SELECT 1 FROM subscriptions WHERE subscriber_id = ? AND creator_id = ?");
    $stmtSub->execute([$_SESSION['user_id'], $video['user_id']]);
    $isSubscribed = (bool)$stmtSub->fetchColumn();
} else {
    $isSubscribed = false;
}
?>

<?php
$stmtSuggested = $db->prepare("
    SELECT videos.*, users.username 
    FROM videos 
    JOIN users ON videos.user_id = users.id 
    WHERE videos.id != ?
    ORDER BY RANDOM()
    LIMIT 10
");
$stmtSuggested->execute([$video_id]);
$suggestedVideos = $stmtSuggested->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="video-page">
    <div class="video-main-content">
        <div class="player-container-wrapper" id="playerWrapper">
            <div class="custom-player" id="customPlayer">
                <video src="api/stream.php?file=<?= urlencode($video['filename']) ?>" id="mainVideo" poster="uploads/posters/<?= htmlspecialchars($video['poster']) ?>" preload="metadata"></video>
                <div class="player-controls">
                    <button class="play-btn" id="playBtn">▶</button>
                    <div class="progress-bar" id="progressBar">
                        <div class="progress-filled" id="progressFilled"></div>
                    </div>
                    <span class="time-display" id="timeDisplay">0:00 / 0:00</span>
                    <button class="mute-btn" id="muteBtn">🔊</button>
                    <input type="range" id="volumeSlider" min="0" max="1" step="0.05" value="1">
                    
                    <div class="quality-selector">
                        <button class="settings-btn" id="settingsBtn">⚙️</button>
                        <div class="quality-menu" id="qualityMenu">
                            <div class="quality-option active" data-quality="auto">Auto</div>
                        </div>
                    </div>

                    <button class="fullscreen-btn" id="fullscreenBtn">⛶</button>
                </div>
            </div>
        </div>

        <div class="video-details">
            <div class="video-header-info">
                <h2><?= htmlspecialchars($video['title']) ?></h2>
                <div class="video-meta-row">
                    <div class="author-info">
                        <div class="user-avatar-small">
                            <?php if ($video['user_avatar']): ?>
                                <img src="uploads/avatars/<?= htmlspecialchars($video['user_avatar']) ?>" alt="Avatar" class="avatar-img">
                            <?php else: ?>
                                <?= strtoupper(substr($video['username'], 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <div class="author-details">
                            <span class="author-name"><?= htmlspecialchars($video['username']) ?></span>
                            <span class="sub-count">Subskrybenci</span>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $video['user_id']): ?>
                            <button class="subscribe-btn <?= $isSubscribed ? 'subscribed' : '' ?>" id="subscribeBtn" data-creator-id="<?= $video['user_id'] ?>">
                                <?= $isSubscribed ? 'Subskrybujesz' : 'Subskrybuj' ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="actions">
                        <button class="like-btn <?= $userLiked ? 'liked' : '' ?>" id="likeBtn" data-video-id="<?= $video['id'] ?>">
                            👍 <span id="likeCount"><?= $likesCount ?></span>
                        </button>
                        <button class="dislike-btn <?= $userDisliked ? 'disliked' : '' ?>" id="dislikeBtn" data-video-id="<?= $video['id'] ?>">
                            👎 <span id="dislikeCount"><?= $dislikesCount ?></span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="description-box">
                <span class="date">Dodano: <?= date('d.m.Y', strtotime($video['created_at'])) ?></span>
                <p><?= nl2br(htmlspecialchars($video['description'])) ?></p>
            </div>
        </div>

        <div class="comments-section">
            <h3>Komentarze</h3>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="comment-form-container">
                    <div class="user-avatar-small">
                        <?php 
                        $stmtMe = $db->prepare("SELECT avatar, username FROM users WHERE id = ?");
                        $stmtMe->execute([$_SESSION['user_id']]);
                        $me = $stmtMe->fetch();
                        if ($me['avatar']): ?>
                            <img src="uploads/avatars/<?= htmlspecialchars($me['avatar']) ?>" alt="Avatar" class="avatar-img">
                        <?php else: ?>
                            <?= strtoupper(substr($me['username'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    <form id="commentForm" class="comment-form">
                        <input type="hidden" id="videoId" value="<?= $video['id'] ?>">
                        <textarea id="commentText" placeholder="Dodaj komentarz..." required></textarea>
                        <div class="comment-form-buttons">
                            <button type="button" id="cancelComment" class="btn-text">Anuluj</button>
                            <button type="submit" class="btn primary">Skomentuj</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php">Zaloguj się</a>, aby dodać komentarz.</p>
            <?php endif; ?>

            <div id="commentsList" class="comments-list">
            </div>
        </div>
    </div>

    <aside class="video-sidebar">
        <h3>Polecane filmy</h3>
        <?php foreach ($suggestedVideos as $suggested): ?>
            <a href="video.php?id=<?= $suggested['id'] ?>" class="suggested-video">
                <img src="uploads/posters/<?= htmlspecialchars($suggested['poster']) ?>" alt="Thumbnail">
                <div class="suggested-info">
                    <h4><?= htmlspecialchars($suggested['title']) ?></h4>
                    <p><?= htmlspecialchars($suggested['username']) ?></p>
                    <p><?= date('d.m.Y', strtotime($suggested['created_at'])) ?></p>
                </div>
            </a>
        <?php endforeach; ?>
        <?php if (empty($suggestedVideos)): ?>
            <p>Brak sugerowanych filmów.</p>
        <?php endif; ?>
    </aside>
</div>

<script src="assets/js/player.js"></script>
<?php require 'includes/footer.php'; ?>