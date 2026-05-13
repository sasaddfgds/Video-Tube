<?php
require 'includes/db.php';
require 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Nazwa użytkownika jest już zajęta.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                header("Location: login.php");
                exit;
            }
        }
    } else {
        $error = "Wypełnij wszystkie pola.";
    }
}
?>

<div class="auth-form">
    <h2>Zarejestruj się</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nazwa użytkownika" required>
        <input type="password" name="password" placeholder="Hasło" required>
        <button type="submit" class="btn primary">Zarejestruj się</button>
    </form>
    <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
</div>

<?php require 'includes/footer.php'; ?>