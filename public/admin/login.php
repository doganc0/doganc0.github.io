<?php
require_once __DIR__ . '/../../bootstrap.php';

$db = Database::getInstance();
$auth = new Auth($db);

if ($auth->check()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($auth->attemptLogin($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Kullanıcı adı veya şifre hatalı.';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magnus Admin Girişi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #fff;
            padding: 2.5rem 2rem;
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(93, 63, 211, 0.12);
            width: min(420px, 90vw);
        }
        .login-card h1 {
            margin: 0 0 1.5rem;
            font-size: 1.5rem;
            text-align: center;
            color: var(--primary);
        }
        .login-card .form-group label {
            font-size: 0.95rem;
        }
        .login-card .btn {
            width: 100%;
        }
        .login-card .error-message {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <form class="login-card" method="post" autocomplete="off">
        <h1>Magnus Admin</h1>
        <?php if ($error): ?>
            <div class="error-message" style="display:block; color:#ff5a5f;"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <div class="form-group">
            <label for="username">Kullanıcı Adı</label>
            <input type="text" id="username" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Şifre</label>
            <input type="password" id="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Giriş Yap</button>
    </form>
</body>
</html>
