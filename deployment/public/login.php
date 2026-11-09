<?php

declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/layout.php';

if (isLoggedIn()) {
    header('Location: /', true, 303);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();

    // Use a single shared throttle for this one-administrator portal.
    // The lock makes concurrent login attempts update the same counter safely.
    $attemptsFile = fopen(RUNTIME_DIR . '/login-attempts.json', 'c+');

    if ($attemptsFile === false || !flock($attemptsFile, LOCK_EX)) {
        http_response_code(503);
        exit('Login temporarily unavailable.');
    }

    rewind($attemptsFile);
    $attempts = json_decode(stream_get_contents($attemptsFile), true);
    $attempts = is_array($attempts) ? $attempts : ['count' => 0, 'until' => 0];

    $now = time();
    $windowEndsAt = (int) ($attempts['until'] ?? 0);

    if ($now >= $windowEndsAt) {
        $attempts = ['count' => 0, 'until' => $now + 900];
    }

    $canAttempt = (int) $attempts['count'] < 10;
    $username = is_string($_POST['username'] ?? null) ? $_POST['username'] : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';

    $credentialsValid = $canAttempt
        && hash_equals((string) $config['username'], $username)
        && password_verify($password, (string) $config['password_hash']);

    if ($credentialsValid) {
        $attempts = ['count' => 0, 'until' => 0];
    } else {
        $attempts['count']++;
    }

    rewind($attemptsFile);
    ftruncate($attemptsFile, 0);
    fwrite($attemptsFile, json_encode($attempts, JSON_THROW_ON_ERROR));
    fflush($attemptsFile);
    flock($attemptsFile, LOCK_UN);
    fclose($attemptsFile);

    if ($credentialsValid) {
        session_regenerate_id(true);
        $_SESSION['authenticated'] = true;
        $_SESSION['last_seen'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        header('Location: /', true, 303);
        exit;
    }

    $error = 'Invalid credentials or login temporarily restricted.';
}

pageStart('Sign in');
?>
<h1>KCDF Deployment Manager</h1>
<p>Administrator sign in</p>

<?php if ($error !== ''): ?>
    <p class="error"><?= escapeHtml($error) ?></p>
<?php endif; ?>

<form method="post">
    <input type="hidden" name="csrf_token" value="<?= escapeHtml(csrfToken()) ?>">

    <label>
        Username
        <input name="username" type="text" autocomplete="username" required>
    </label>

    <label>
        Password
        <input name="password" type="password" autocomplete="current-password" required>
    </label>

    <button type="submit">Sign in</button>
</form>
<?php pageEnd(); ?>
