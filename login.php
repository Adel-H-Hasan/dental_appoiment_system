<?php
$root = '';
require __DIR__ . '/config.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter your email and password.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Wrong email or password.";
        } elseif ($user['is_active'] != 1) {
            $error = "Your account is deactivated. Please contact the clinic.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }
}

$title = 'Login';
include __DIR__ . '/header.php';
?>
<div class="card form-card">
    <h2>Login</h2>
    <?php if ($error !== ''): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <label>Email</label>
        <input type="text" name="email" value="<?= e($email) ?>">
        <label>Password</label>
        <input type="password" name="password">
        <button type="submit">Login</button>
    </form>
</div>
<?php include __DIR__ . '/footer.php'; ?>
