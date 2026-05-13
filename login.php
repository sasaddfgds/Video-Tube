<?php
require 'includes/db.php';
require 'includes/header.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Nieprawidłowa nazwa użytkownika lub hasło.";
    }
}
?>

<div class="auth-form">
    <h2>Zaloguj się</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nazwa użytkownika" required>
        <input type="password" name="password" placeholder="Hasło" required>
        <button type="submit" class="btn primary">Zaloguj się</button>
    </form>
    <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
</div>

<?php require 'includes/footer.php'; ?>