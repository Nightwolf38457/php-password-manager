<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/KeyManager.php';
require_once __DIR__ . '/../classes/User.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user     = new User();
    $result   = $user->login($username, $password);
    if ($result) {
        $_SESSION['user_id']        = $result['id'];
        $_SESSION['username']       = $result['username'];
        $_SESSION['encrypted_key']  = $result['encrypted_key'];
        $_SESSION['plain_password'] = $password;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex;
               justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 2rem; border-radius: 8px;
               box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
        h2 { margin-bottom: 1.5rem; text-align: center; }
        input { width: 100%; padding: 10px; margin-bottom: 1rem;
                border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2196F3; color: white;
                 border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #1976D2; }
        .error   { color: red;   margin-bottom: 1rem; text-align: center; }
        .success { color: green; margin-bottom: 1rem; text-align: center; }
        .link    { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="box">
    <h2>Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if (isset($_GET['registered'])): ?><p class="success">Account created! Please login.</p><?php endif; ?>
    <form method="POST">
        <input type="text"     name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <div class="link"><a href="register.php">No account? Register</a></div>
</div>
</body>
</html>