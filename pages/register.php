<?php
session_start();
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/KeyManager.php';
require_once __DIR__ . '/../classes/User.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $user = new User();
        try {
            if ($user->register($username, $password)) {
                header('Location: login.php?registered=1');
                exit;
            }
        } catch (Exception $e) {
            $error = 'Username already taken. Choose another.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex;
               justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .box { background: white; padding: 2rem; border-radius: 8px;
               box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 320px; }
        h2 { margin-bottom: 1.5rem; text-align: center; }
        input { width: 100%; padding: 10px; margin-bottom: 1rem;
                border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #4CAF50; color: white;
                 border: none; border-radius: 4px; cursor: pointer; font-size: 1rem; }
        button:hover { background: #45a049; }
        .error { color: red; margin-bottom: 1rem; text-align: center; }
        .link  { text-align: center; margin-top: 1rem; }
    </style>
</head>
<body>
<div class="box">
    <h2>Register</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
        <input type="text"     name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Create Account</button>
    </form>
    <div class="link"><a href="login.php">Already have an account? Login</a></div>
</div>
</body>
</html>