<?php
require 'includes/db.php';
$stmt = $db->query("SELECT id, title, filename FROM videos ORDER BY id ASC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Automated Player Test</title>
    <style>
        body { font-family: sans-serif; background: #111; color: #eee; padding: 20px; }
        .log-container { height: 400px; overflow-y: auto; background: #222; padding: 10px; border: 1px solid #444; font-family: monospace; }
        .success { color: #0f0; }
        .error { color: #f00; }
        video { max-width: 600px; background: #000; display: block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Automatyczny Test Odtwarzacza (20 wideo)</h1>
    <video id="testVideo" controls muted></video>
    <button id="startTestBtn" style="padding: 10px 20px; font-size: 16px; margin-bottom: 20px;">Rozpocznij Test</button>
    <div class="log-container" id="logContainer"></div>

    <script>
        const videos = <?= json_encode($videos) ?>;
        const videoElement = document.getElementById('testVideo');
        const logContainer = document.getElementById('logContainer');
        const startBtn = document.getElementById('startTestBtn');
        let currentIndex = 0;

        function log(msg, type = 'info') {
            const div = document.createElement('div');
            div.className = type;
            div.textContent = `[${new Date().toLocaleTimeString()}] ${msg}`;
            logContainer.appendChild(div);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        async function testNextVideo() {
            if (currentIndex >= videos.length) {
                log('=== Test Zakończony ===', 'success');
                return;
            }

            const video = videos[currentIndex];
            log(`Testowanie wideo ${currentIndex + 1}/${videos.length}: ${video.title} (${video.filename})`);
            
            videoElement.src = 'uploads/videos/' + video.filename;
            
            try {
                await new Promise((resolve, reject) => {
                    const timeout = setTimeout(() => reject('Timeout ładowania wideo'), 10000);
                    
                    videoElement.onloadeddata = () => {
                        log(`  Dane załadowane. Rozdzielczość: ${videoElement.videoWidth}x${videoElement.videoHeight}`);
                    };

                    videoElement.onplaying = () => {
                        clearTimeout(timeout);
                        log(`  Odtwarzanie rozpoczęte poprawnie.`, 'success');
                        setTimeout(resolve, 2000); // Play for 2 seconds
                    };

                    videoElement.onerror = (e) => {
                        clearTimeout(timeout);
                        reject(`Błąd odtwarzania: ${videoElement.error.code} - ${videoElement.error.message}`);
                    };

                    videoElement.play().catch(e => {
                        clearTimeout(timeout);
                        reject(`Autoplay zablokowany lub błąd: ${e.message}`);
                    });
                });
                
                log(`  Test wideo ${video.id} zaliczony.`, 'success');
            } catch (err) {
                log(`  BŁĄD wideo ${video.id}: ${err}`, 'error');
            }

            videoElement.pause();
            currentIndex++;
            setTimeout(testNextVideo, 500);
        }

        startBtn.addEventListener('click', () => {
            startBtn.disabled = true;
            log('=== Rozpoczęcie Testu ===');
            testNextVideo();
        });
    </script>
</body>
</html>