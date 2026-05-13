document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;

    const currentTheme = localStorage.getItem('theme');
    if (currentTheme === 'dark') {
        body.classList.add('dark-theme');
    }

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const theme = body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', theme);
        });
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
        }
    }

    // Settings Menu Toggle
    const settingsBtnHeader = document.getElementById('settingsBtnHeader');
    const settingsMenu = document.getElementById('settingsMenu');

    if (settingsBtnHeader && settingsMenu) {
        settingsBtnHeader.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsMenu.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!settingsMenu.contains(e.target) && e.target !== settingsBtnHeader) {
                settingsMenu.classList.remove('show');
            }
        });
    }


    const changeUsernameBtn = document.getElementById('changeUsernameBtn');
    const usernameModal = document.getElementById('usernameModal');
    const closeUsernameModal = document.getElementById('closeUsernameModal');
    const saveUsernameBtn = document.getElementById('saveUsernameBtn');
    const newUsernameInput = document.getElementById('newUsernameInput');

    if (changeUsernameBtn && usernameModal) {
        changeUsernameBtn.addEventListener('click', () => {
            usernameModal.classList.add('show');
            settingsMenu.classList.remove('show');
        });

        closeUsernameModal.addEventListener('click', () => {
            usernameModal.classList.remove('show');
        });

        saveUsernameBtn.addEventListener('click', async () => {
            const newUsername = newUsernameInput.value.trim();
            if (!newUsername) return;

            const response = await fetch('api/change_username.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username: newUsername })
            });

            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.message);
            }
        });

        window.addEventListener('click', (e) => {
            if (e.target === usernameModal) {
                usernameModal.classList.remove('show');
            }
        });
    }

    // Search Live Logic

    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(this, args);
            }, delay);
        };
    }

    const performSearch = async (query) => {
        if (query.length < 2) {
            searchResults.innerHTML = '';
            searchResults.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`api/search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            searchResults.innerHTML = '';
            if (data.length > 0) {
                data.forEach(video => {
                    const div = document.createElement('div');
                    div.classList.add('search-item');
                    div.innerHTML = `
                        <a href="video.php?id=${video.id}">
                            <span class="title">${video.title}</span>
                            <span class="author">${video.username}</span>
                        </a>
                    `;
                    searchResults.appendChild(div);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div class="search-item">Brak wyników</div>';
                searchResults.style.display = 'block';
            }
        } catch (error) {
            console.error('Błąd wyszukiwania:', error);
        }
    };

    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            performSearch(e.target.value);
        }, 300));
    }

    const subscribeBtn = document.getElementById('subscribeBtn');
    if (subscribeBtn) {
        subscribeBtn.addEventListener('click', async () => {
            const creatorId = subscribeBtn.dataset.creatorId;
            const formData = new FormData();
            formData.append('creator_id', creatorId);

            try {
                const response = await fetch('api/subscribe.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.error === 'not_logged_in') {
                    window.location.href = 'login.php';
                    return;
                }

                if (data.status === 'subscribed') {
                    subscribeBtn.classList.add('subscribed');
                    subscribeBtn.textContent = 'Subskrybujesz';
                } else if (data.status === 'unsubscribed') {
                    subscribeBtn.classList.remove('subscribed');
                    subscribeBtn.textContent = 'Subskrybuj';
                }
            } catch (error) {
                console.error('Błąd subskrypcji:', error);
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const videoId = urlParams.get('id');
    if (window.location.pathname.includes('video.php') && videoId) {
        let history = JSON.parse(localStorage.getItem('videoHistory') || '[]');
        if (!history.includes(videoId)) {
            history.unshift(videoId);
            if (history.length > 20) history.pop();
            localStorage.setItem('videoHistory', JSON.stringify(history));
        }
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            if (searchResults) {
                searchResults.style.display = 'none';
            }
        }
    });
});