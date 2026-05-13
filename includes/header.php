<?php
session_start();
require_once __DIR__ . '/db.php';
$headerAvatar = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $headerAvatar = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video-Tube</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Poppins:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-left">
            <button id="sidebarToggle" class="sidebar-toggle">☰</button>
            <a href="index.php" class="logo">
                <span class="logo-icon">▶</span>
                Video<span class="logo-highlight">Tube</span>
            </a>
        </div>
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Szukaj...">
            <div id="searchResults" class="search-results"></div>
        </div>
        <div class="nav-links">
            <button id="themeToggle" class="btn">🌓</button>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="upload.php" class="btn primary">Prześlij</a>
                
                <div class="settings-dropdown">
                    <button id="settingsBtnHeader" class="btn settings-icon-btn">⚙️</button>
                    <div id="settingsMenu" class="settings-menu">
                        <form id="headerAvatarForm" action="api/upload_avatar.php" method="POST" enctype="multipart/form-data">
                            <label for="headerAvatarInput" class="settings-menu-item">
                                <span>🖼️ Zmień awatar</span>
                                <input type="file" name="avatar" id="headerAvatarInput" accept="image/*" hidden onchange="this.form.submit()">
                            </label>
                        </form>
                        <div id="changeUsernameBtn" class="settings-menu-item">✏️ Zmień nazwę</div>
                        <a href="profile.php" class="settings-menu-item">👤 Mój profil</a>
                        <hr class="menu-divider">
                        <a href="logout.php" class="settings-menu-item logout">🚪 Wyloguj się</a>
                    </div>
                </div>

                <a href="profile.php" class="user-avatar-small">
                    <?php if ($headerAvatar): ?>
                        <img src="uploads/avatars/<?= htmlspecialchars($headerAvatar) ?>" alt="Avatar" class="avatar-img">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn">Zaloguj się</a>
            <?php endif; ?>
        </div>

        <div id="usernameModal" class="modal">
            <div class="modal-content">
                <h3>Zmień nazwę użytkownika</h3>
                <input type="text" id="newUsernameInput" placeholder="Nowa nazwa" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>">
                <div class="modal-buttons">
                    <button id="closeUsernameModal" class="btn">Anuluj</button>
                    <button id="saveUsernameBtn" class="btn primary">Zapisz</button>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-section">
                <a href="index.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                    <span class="icon">🏠</span>
                    <span class="label">Główna</span>
                </a>
                <a href="trending.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'trending.php' ? 'active' : '' ?>">
                    <span class="icon">🔥</span>
                    <span class="label">Trendy</span>
                </a>
                <a href="subscriptions.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'subscriptions.php' ? 'active' : '' ?>">
                    <span class="icon">📺</span>
                    <span class="label">Subskrypcje</span>
                </a>
            </div>
            <hr class="sidebar-divider">
            <div class="sidebar-section">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
                        <span class="icon">👤</span>
                        <span class="label">Mój profil</span>
                    </a>
                    <a href="my_videos.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'my_videos.php' ? 'active' : '' ?>">
                        <span class="icon">🎞️</span>
                        <span class="label">Moje filmy</span>
                    </a>
                    <a href="history.php" class="sidebar-item <?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : '' ?>">
                        <span class="icon">🕒</span>
                        <span class="label">Historia</span>
                    </a>
                <?php else: ?>
                    <p class="sidebar-text">Zaloguj się, aby lajkować filmy, pisać komentarze i subskrybować kanały.</p>
                    <a href="login.php" class="btn login-sidebar-btn">Zaloguj się</a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="container">