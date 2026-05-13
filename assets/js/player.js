document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('mainVideo');
    const playBtn = document.getElementById('playBtn');
    const muteBtn = document.getElementById('muteBtn');
    const volumeSlider = document.getElementById('volumeSlider');
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    const progressBar = document.getElementById('progressBar');
    const progressFilled = document.getElementById('progressFilled');
    const playerWrapper = document.getElementById('playerWrapper');
    const customPlayer = document.getElementById('customPlayer');
    const timeDisplay = document.getElementById('timeDisplay');

    const formatTime = (time) => {
        if (isNaN(time)) return "0:00";
        const minutes = Math.floor(time / 60);
        const seconds = Math.floor(time % 60);
        return `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
    };

    if (video && playBtn) {
        const togglePlay = () => {
            if (video.paused) {
                video.play();
                playBtn.textContent = '⏸';
            } else {
                video.pause();
                playBtn.textContent = '▶';
            }
        };

        playBtn.addEventListener('click', togglePlay);
        video.addEventListener('click', togglePlay);

        muteBtn.addEventListener('click', () => {
            video.muted = !video.muted;
            muteBtn.textContent = video.muted ? '🔇' : '🔊';
        });

        volumeSlider.addEventListener('input', (e) => {
            video.volume = e.target.value;
            if (video.volume === 0) {
                video.muted = true;
                muteBtn.textContent = '🔇';
            } else {
                video.muted = false;
                muteBtn.textContent = '🔊';
            }
        });

        video.addEventListener('timeupdate', () => {
            if (video.duration && !isDragging) {
                const percentage = (video.currentTime / video.duration) * 100;
                progressFilled.style.width = `${percentage}%`;
                if (timeDisplay) {
                    timeDisplay.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
                }
            }
        });

        const updateDuration = () => {
            if (timeDisplay && video.duration) {
                timeDisplay.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
            }
        };

        video.addEventListener('loadedmetadata', updateDuration);
        video.addEventListener('durationchange', updateDuration);
        
        if (video.readyState >= 1) {
            updateDuration();
        }

        let isDragging = false;

        const scrub = (e) => {
            if (!video.duration) return;
            const rect = progressBar.getBoundingClientRect();
            let pos = (e.clientX - rect.left) / progressBar.offsetWidth;
            pos = Math.max(0, Math.min(1, pos));
            
            const percentage = pos * 100;
            progressFilled.style.width = `${percentage}%`;
            
            video.currentTime = pos * video.duration;
            if (timeDisplay) {
                timeDisplay.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
            }
        };

        progressBar.addEventListener('mousedown', (e) => {
            isDragging = true;
            scrub(e);
        });

        window.addEventListener('mousemove', (e) => {
            if (isDragging) scrub(e);
        });

        window.addEventListener('mouseup', () => {
            isDragging = false;
        });

        progressBar.addEventListener('click', scrub);

        fullscreenBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                customPlayer.requestFullscreen().catch(err => {
                    console.error('Błąd pełnego ekranu', err);
                });
            } else {
                document.exitFullscreen();
            }
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting && !video.paused) {
                    customPlayer.classList.add('sticky');
                } else {
                    customPlayer.classList.remove('sticky');
                }
            });
        }, { threshold: 0.1 });

        observer.observe(playerWrapper);
        
        const settingsBtn = document.getElementById('settingsBtn');
        const qualityMenu = document.getElementById('qualityMenu');
        const qualityOptions = document.querySelectorAll('.quality-option');
        
        if (settingsBtn && qualityMenu) {
            settingsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                qualityMenu.classList.toggle('show');
            });
            
            document.addEventListener('click', () => {
                qualityMenu.classList.remove('show');
            });
        }
    }

    const likeBtn = document.getElementById('likeBtn');
    const likeCount = document.getElementById('likeCount');
    const dislikeBtn = document.getElementById('dislikeBtn');
    const dislikeCount = document.getElementById('dislikeCount');

    if (likeBtn) {
        likeBtn.addEventListener('click', async () => {
            const videoId = likeBtn.dataset.videoId;
            try {
                const response = await fetch('api/like.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ video_id: videoId })
                });
                const data = await response.json();
                
                if (data.error) {
                    if(data.error === 'not_logged_in') window.location.href = 'login.php';
                    else alert(data.error);
                    return;
                }

                likeCount.textContent = data.likes;
                dislikeCount.textContent = data.dislikes;
                
                if (data.action === 'liked') {
                    likeBtn.classList.add('liked');
                    dislikeBtn.classList.remove('disliked');
                } else {
                    likeBtn.classList.remove('liked');
                }
            } catch (error) {
                console.error('Błąd polubienia:', error);
            }
        });
    }

    if (dislikeBtn) {
        dislikeBtn.addEventListener('click', async () => {
            const videoId = dislikeBtn.dataset.videoId;
            try {
                const response = await fetch('api/dislike.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ video_id: videoId })
                });
                const data = await response.json();
                
                if (data.error) {
                    if(data.error === 'not_logged_in') window.location.href = 'login.php';
                    else alert(data.error);
                    return;
                }

                likeCount.textContent = data.likes;
                dislikeCount.textContent = data.dislikes;
                
                if (data.action === 'disliked') {
                    dislikeBtn.classList.add('disliked');
                    likeBtn.classList.remove('liked');
                } else {
                    dislikeBtn.classList.remove('disliked');
                }
            } catch (error) {
                console.error('Błąd dislike:', error);
            }
        });
    }

    const commentForm = document.getElementById('commentForm');
    const commentText = document.getElementById('commentText');
    const commentsList = document.getElementById('commentsList');
    const videoIdInput = document.getElementById('videoId');

    const loadComments = async () => {
        if (!videoIdInput) return;
        const videoId = videoIdInput.value;
        try {
            const response = await fetch(`api/get_comments.php?video_id=${videoId}`);
            const comments = await response.json();
            
            commentsList.innerHTML = '';
            
            const renderComment = (comment, isReply = false) => {
                const commentDiv = document.createElement('div');
                commentDiv.classList.add('comment-item');
                if (isReply) commentDiv.classList.add('comment-reply');
                
                const avatarPart = comment.avatar 
                    ? `<img src="uploads/avatars/${comment.avatar}" class="comment-avatar">`
                    : `<div class="comment-avatar-placeholder">${comment.username.charAt(0).toUpperCase()}</div>`;

                commentDiv.innerHTML = `
                    ${avatarPart}
                    <div class="comment-content">
                        <div class="comment-header">
                            <span class="comment-author">${comment.username}</span>
                            <span class="comment-date">${comment.created_at}</span>
                        </div>
                        <div class="comment-text">${comment.text}</div>
                        <div class="comment-actions">
                            <button class="comment-like ${comment.user_vote === 'like' ? 'active' : ''}" data-id="${comment.id}">
                                👍 <span>${comment.likes}</span>
                            </button>
                            <button class="comment-dislike ${comment.user_vote === 'dislike' ? 'active' : ''}" data-id="${comment.id}">
                                👎 <span>${comment.dislikes}</span>
                            </button>
                            <button class="comment-reply-btn" data-id="${comment.id}">ODPOWIEDZ</button>
                        </div>
                        <div class="reply-form-container" id="reply-form-${comment.id}"></div>
                        <div class="replies-container"></div>
                    </div>
                `;

                const likeBtn = commentDiv.querySelector('.comment-like');
                const dislikeBtn = commentDiv.querySelector('.comment-dislike');
                const replyBtn = commentDiv.querySelector('.comment-reply-btn');

                likeBtn.onclick = () => voteComment(comment.id, 'like');
                dislikeBtn.onclick = () => voteComment(comment.id, 'dislike');
                replyBtn.onclick = () => showReplyForm(comment.id);

                if (comment.replies && comment.replies.length > 0) {
                    const repliesContainer = commentDiv.querySelector('.replies-container');
                    comment.replies.forEach(reply => {
                        repliesContainer.appendChild(renderComment(reply, true));
                    });
                }

                return commentDiv;
            };

            comments.forEach(comment => {
                commentsList.appendChild(renderComment(comment));
            });
        } catch (error) {
            console.error('Błąd pobierania komentarzy:', error);
        }
    };

    const voteComment = async (commentId, type) => {
        try {
            const response = await fetch('api/like_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ comment_id: commentId, type: type })
            });
            const data = await response.json();
            if (data.error) {
                if(data.error === 'not_logged_in') window.location.href = 'login.php';
                else alert(data.error);
                return;
            }
            loadComments();
        } catch (e) { console.error(e); }
    };

    const showReplyForm = (commentId) => {
        const container = document.getElementById(`reply-form-${commentId}`);
        if (container.innerHTML !== '') {
            container.innerHTML = '';
            return;
        }
        
        container.innerHTML = `
            <div class="reply-input-box">
                <textarea placeholder="Dodaj odpowiedź..." id="reply-text-${commentId}"></textarea>
                <div class="reply-buttons">
                    <button class="cancel-reply" onclick="document.getElementById('reply-form-${commentId}').innerHTML=''">ANULUJ</button>
                    <button class="submit-reply" data-id="${commentId}">ODPOWIEDZ</button>
                </div>
            </div>
        `;

        container.querySelector('.submit-reply').onclick = async () => {
            const text = document.getElementById(`reply-text-${commentId}`).value;
            if (!text.trim()) return;

            const response = await fetch('api/add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ video_id: videoIdInput.value, parent_id: commentId, text: text })
            });
            const data = await response.json();
            if (data.success) loadComments();
        };
    };

    if (commentForm) {
        commentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const videoId = videoIdInput.value;
            const text = commentText.value;

            try {
                const response = await fetch('api/add_comment.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ video_id: videoId, text: text })
                });
                const data = await response.json();

                if (data.error) {
                    alert(data.error);
                    return;
                }

                commentText.value = '';
                loadComments();
            } catch (error) {
                console.error('Błąd dodawania komentarza:', error);
            }
        });
    }

    if (commentsList) {
        loadComments();
    }
});