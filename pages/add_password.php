<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/KeyManager.php';
require_once __DIR__ . '/../classes/PasswordVault.php';
require_once __DIR__ . '/../classes/PasswordGenerator.php';

$error = $generated = '';

if (isset($_POST['generate'])) {
    $gen       = new PasswordGenerator();
    $generated = $gen->generate(
        (int)($_POST['gen_upper']   ?? 2),
        (int)($_POST['gen_lower']   ?? 3),
        (int)($_POST['gen_digits']  ?? 2),
        (int)($_POST['gen_special'] ?? 2)
    );
}
if (isset($_POST['save'])) {
    $siteName     = trim($_POST['site_name']      ?? '');
    $sitePassword = trim($_POST['site_password']  ?? '');
    if ($siteName && $sitePassword) {
        $vault = new PasswordVault();
        $vault->addPassword(
            $_SESSION['user_id'],
            $_SESSION['encrypted_key'],
            $_SESSION['plain_password'],
            $siteName,
            $sitePassword
        );
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Password</title>
    <style>
        body  { font-family: Arial, sans-serif; background: #f0f2f5;
                display: flex; justify-content: center; padding: 40px 20px; margin: 0; }
        .box  { background: white; padding: 2rem; border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 440px; }
        h2    { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 4px; font-weight: bold; font-size: 0.9rem; }
        input[type=text], input[type=number] {
            width: 100%; padding: 9px; margin-bottom: 1rem;
            border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .row  { display: flex; gap: 10px; }
        .row > div { flex: 1; }
        button { padding: 10px 18px; border: none; border-radius: 4px;
                 cursor: pointer; font-size: 0.95rem; }
        .btn-blue  { background: #2196F3; color: white; width: 100%; margin-bottom: 8px; }
        .btn-green { background: #4CAF50; color: white; width: 100%; }
        .btn-grey  { background: #9e9e9e; color: white; width: 100%; margin-top: 8px; }
        .generated { background: #e8f5e9; border: 1px solid #a5d6a7; padding: 10px;
                     border-radius: 4px; font-family: monospace; font-size: 1.1rem;
                     margin-bottom: 1rem; word-break: break-all; }
        .error { color: red; margin-bottom: 1rem; }
        hr { margin: 1.5rem 0; border: none; border-top: 1px solid #eee; }
    </style>
</head>
<body>
<div class="box">
    <h2>Add Password</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>

    <form method="POST">
        <label>Site / App Name</label>
        <input type="text" name="site_name" placeholder="e.g. Gmail, Facebook"
               value="<?= htmlspecialchars($_POST['site_name'] ?? '') ?>" required>

        <hr>
        <label>Generate a Password</label>
        <div class="row">
            <div><label>Uppercase</label><input type="number" name="gen_upper"   min="0" max="20" value="<?= (int)($_POST['gen_upper']   ?? 2) ?>"></div>
            <div><label>Lowercase</label><input type="number" name="gen_lower"   min="0" max="20" value="<?= (int)($_POST['gen_lower']   ?? 3) ?>"></div>
            <div><label>Digits</label>   <input type="number" name="gen_digits"  min="0" max="20" value="<?= (int)($_POST['gen_digits']  ?? 2) ?>"></div>
            <div><label>Special</label>  <input type="number" name="gen_special" min="0" max="20" value="<?= (int)($_POST['gen_special'] ?? 2) ?>"></div>
        </div>
        <button type="submit" name="generate" class="btn-blue">Generate</button>

        <?php if ($generated): ?>
            <div class="generated"><?= htmlspecialchars($generated) ?></div>
        <?php endif; ?>

        <hr>
        <label>Password to Save</label>
        <input type="text" name="site_password" placeholder="Paste generated or type your own"
               value="<?= htmlspecialchars($generated ?: ($_POST['site_password'] ?? '')) ?>">

        <button type="submit" name="save" class="btn-green">Save Password</button>
        <a href="dashboard.php"><button type="button" class="btn-grey">← Back</button></a>
    </form>
</div>
</body>
</html>