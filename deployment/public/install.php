<?php

declare(strict_types=1);

require __DIR__ . '/../includes/installer.php';

if (installerInstalled()) {
    header('Location: /login.php', true, 303);
    exit;
}
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    http_response_code(403);
    exit('HTTPS is required for installation.');
}

header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
ini_set('session.use_strict_mode', '1');
session_name('kcdf_deploy_setup');
session_set_cookie_params(['secure' => true, 'httponly' => true, 'samesite' => 'Strict', 'path' => '/']);
session_start();
$_SESSION['setup_csrf'] ??= bin2hex(random_bytes(32));
$errors = [];
$complete = false;
$defaults = [
    'username' => 'admin', 'repo_url' => 'git@github.com:ORG/REPOSITORY.git',
    'branch' => 'main', 'repo_dir' => '/home/kcdf/deployment/repo',
    'api_source' => 'api-backend-php',
    'admin_source' => 'kcdf-admin-app/dist/kcdf-admin-app/browser',
    'member_source' => 'kcdf-parents-app/www',
    'api_dest' => '/home/kcdf/web/api.kcdfindia.com/public_html',
    'admin_dest' => '/home/kcdf/web/admin.kcdfindia.com/public_html',
    'member_dest' => '/home/kcdf/web/YOUR_MEMBER_DOMAIN/public_html',
];
$values = $defaults;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($defaults as $key => $default) {
        $values[$key] = is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';
    }
    $values['password'] = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $values['confirm_password'] = is_string($_POST['confirm_password'] ?? null) ? $_POST['confirm_password'] : '';

    if (!is_string($_POST['csrf_token'] ?? null)
        || !hash_equals($_SESSION['setup_csrf'], $_POST['csrf_token'])) {
        $errors[] = 'Invalid form token.';
    }
    $errors = array_merge($errors, installerValidate($values));
    if (!$errors) {
        $lock = fopen(__DIR__ . '/../runtime/install-process.lock', 'c');
        if ($lock === false || !flock($lock, LOCK_EX)) {
            $errors[] = 'Unable to lock installation.';
        } else {
            try {
                installerComplete($values);
                $complete = true;
                $_SESSION = [];
            } catch (Throwable $exception) {
                error_log('KCDF installation failed: ' . $exception->getMessage());
                $errors[] = 'Installation could not be completed. Check permissions and server logs.';
            } finally {
                flock($lock, LOCK_UN);
                fclose($lock);
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Install · KCDF Deployment Manager</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<main class="container"><section class="panel">
    <h1>KCDF Deployment Manager — Installation</h1>
    <?php if ($complete): ?>
        <p>Installation completed. The installation lock has been created.</p>
        <p><a href="/login.php">Continue to login</a></p>
    <?php else: ?>
        <p>Enter the initial settings. Deployment will start in dry-run mode. Only open this installer while access to the site is restricted to you.</p>
        <?php foreach ($errors as $error): ?>
            <p class="error"><?= installerEscape($error) ?></p>
        <?php endforeach; ?>
        <form method="post" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?= installerEscape($_SESSION['setup_csrf']) ?>">
            <h2>1. Administrator account</h2>
            <label>Administrator username <input name="username" value="<?= installerEscape($values['username']) ?>" required></label>
            <label>Password (minimum 12 characters) <input type="password" name="password" minlength="12" required autocomplete="new-password"></label>
            <label>Confirm password <input type="password" name="confirm_password" minlength="12" required autocomplete="new-password"></label>
            <h2>2. Git repository</h2>
            <?php foreach (['repo_url' => 'Repository URL', 'branch' => 'Branch', 'repo_dir' => 'Local checkout path'] as $key => $label): ?>
                <label><?= installerEscape($label) ?><input name="<?= $key ?>" value="<?= installerEscape($values[$key]) ?>" required></label>
            <?php endforeach; ?>
            <h2>3. Application paths</h2>
            <?php foreach (['api_source' => 'PHP source (relative to checkout)', 'api_dest' => 'PHP destination', 'admin_source' => 'Angular compiled source', 'admin_dest' => 'Angular destination', 'member_source' => 'Ionic compiled source', 'member_dest' => 'Ionic destination'] as $key => $label): ?>
                <label><?= installerEscape($label) ?><input name="<?= $key ?>" value="<?= installerEscape($values[$key]) ?>" required></label>
            <?php endforeach; ?>
            <button type="submit">Validate and complete installation</button>
        </form>
    <?php endif; ?>
</section></main>
</body></html>
