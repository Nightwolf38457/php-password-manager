<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/KeyManager.php';
require_once __DIR__ . '/../classes/PasswordVault.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $vault = new PasswordVault();
    $vault->deletePassword((int)$_POST['delete_id'], $_SESSION['user_id']);
    header('Location: dashboard.php');
    exit;
}
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$vault     = new PasswordVault();
$passwords = $vault->getPasswords(
    $_SESSION['user_id'],
    $_SESSION['encrypted_key'],
    $_SESSION['plain_password']
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body  { font-family: Arial, sans-serif; background: #f0f2f5; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center;
                  background: white; padding: 1rem 2rem; border-radius: 8px;
                  margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h2 { margin: 0; }
        a.btn, button.btn { padding: 8px 16px; border-radius: 4px; border: none;
                            cursor: pointer; text-decoration: none; font-size: 0.9rem; }
        .btn-green { background: #4CAF50; color: white; }
        .btn-red   { background: #f44336; color: white; }
        table { width: 100%; border-collapse: collapse; background: white;
                border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th { background: #2196F3; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        tr:last-child td { border-bottom: none; }
        .empty { text-align: center; padding: 2rem; background: white;
                 border-radius: 8px; color: #666; }
        .pw { font-family: monospace; letter-spacing: 1px; }
    </style>
</head>
<body>
<div class="header">
    <h2>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h2>
    <div>
        <a href="add_password.php" class="btn btn-green">+ Add Password</a>
        &nbsp;
        <a href="dashboard.php?logout=1" class="btn btn-red">Logout</a>
    </div>
</div>

<?php if (empty($passwords)): ?>
    <div class="empty"><p>No passwords saved yet. <a href="add_password.php">Add one now</a>.</p></div>
<?php else: ?>
    <table>
        <thead>
            <tr><th>#</th><th>Site / App</th><th>Password</th><th>Saved On</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php foreach ($passwords as $i => $p): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= htmlspecialchars($p['site_name']) ?></td>
                <td class="pw"><?= htmlspecialchars($p['plain_password']) ?></td>
                <td><?= $p['created_at'] ?></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this entry?')">
                        <input type="hidden" name="delete_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-red">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
</body>
</html>