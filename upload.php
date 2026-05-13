<?php
require 'includes/db.php';
require 'includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST) && $_SERVER['CONTENT_LENGTH'] > 0) {
        $error = "Plik jest zbyt duży. Maksymalny rozmiar to 2GB.";
    } else {
        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $video = isset($_FILES['video']) ? $_FILES['video'] : null;
        $poster = isset($_FILES['poster']) ? $_FILES['poster'] : null;

        if ($title && $video && $video['error'] === 0 && $poster && $poster['error'] === 0) {
            $videoExt = pathinfo($video['name'], PATHINFO_EXTENSION);
            $posterExt = pathinfo($poster['name'], PATHINFO_EXTENSION);

            $videoName = uniqid() . '.' . $videoExt;
            $posterName = uniqid() . '.' . $posterExt;

            $videoPath = 'uploads/videos/' . $videoName;
            $posterPath = 'uploads/posters/' . $posterName;

            if (move_uploaded_file($video['tmp_name'], $videoPath) && move_uploaded_file($poster['tmp_name'], $posterPath)) {
                $stmt = $db->prepare("INSERT INTO videos (user_id, title, description, filename, poster) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$_SESSION['user_id'], $title, $description, $videoName, $posterName])) {
                    $success = "Wideo zostało pomyślnie przesłane!";
                } else {
                    $error = "Błąd zapisu do bazy danych.";
                }
            } else {
                $error = "Błąd przesyłania plików.";
            }
        } else {
            $error = "Wypełnij wszystkie wymagane pola i upewnij się, że pliki zostały wybrane.";
        }
    }
}
?>

<div class="upload-container">
    <div class="auth-form upload-box">
        <h2>Prześlij nowe wideo</h2>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <?php if ($success): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="title" placeholder="Tytuł wideo" required>
            </div>
            <div class="form-group">
                <textarea name="description" placeholder="Opis wideo" rows="4"></textarea>
            </div>
            
            <div class="file-inputs">
                <div class="file-group">
                    <label for="video">Plik wideo (MP4)</label>
                    <input type="file" name="video" id="video" accept="video/mp4" required>
                </div>
                
                <div class="file-group">
                    <label for="poster">Miniaturka (JPG, PNG)</label>
                    <input type="file" name="poster" id="poster" accept="image/*" required>
                </div>
            </div>
            
            <button type="submit" class="btn primary upload-btn">Opublikuj wideo</button>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>