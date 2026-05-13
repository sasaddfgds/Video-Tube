<?php
require 'includes/db.php';
require 'includes/header.php';
?>

<div class="page-header">
    <h2>🕒 Historia</h2>
    <p>Ostatnio oglądane filmy</p>
</div>

<div id="historyGrid" class="video-grid">
    <div class="empty-state">
        <p>Ładowanie historii...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', async () => {
    const history = JSON.parse(localStorage.getItem('videoHistory') || '[]');
    const grid = document.getElementById('historyGrid');

    if (history.length === 0) {
        grid.innerHTML = '<div class="empty-state"><p>Twoja historia jest pusta.</p></div>';
        return;
    }

    try {
        const response = await fetch('api/get_videos_by_ids.php?ids=' + history.join(','));
        const videos = await response.json();

        if (videos.length === 0) {
            grid.innerHTML = '<div class="empty-state"><p>Nie znaleziono filmów w historii.</p></div>';
            return;
        }

        grid.innerHTML = '';
        videos.forEach(video => {
            const card = document.createElement('div');
            card.className = 'video-card';
            card.innerHTML = `
                <a href="video.php?id=${video.id}">
                    <img src="uploads/posters/${video.poster}" alt="Poster" class="video-thumbnail">
                    <div class="video-info">
                        <h3>${video.title}</h3>
                        <p class="video-author">${video.username}</p>
                        <p class="video-date">${new Date(video.created_at).toLocaleDateString()}</p>
                    </div>
                </a>
            `;
            grid.appendChild(card);
        });
    } catch (err) {
        grid.innerHTML = '<div class="empty-state"><p>Błąd podczas ładowania historii.</p></div>';
    }
});
</script>

<?php require 'includes/footer.php'; ?>