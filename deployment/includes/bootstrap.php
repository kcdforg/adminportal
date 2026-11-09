<?php

declare(strict_types=1);

/**
 * Shared initialization for all public pages.
 *
 * The configuration and runtime directory are intentionally outside the
 * public document root. Do not serve this file directly over HTTP.
 */
const APP_ROOT = __DIR__ . '/..';
const RUNTIME_DIR = APP_ROOT . '/runtime';

$configPath = APP_ROOT . '/config/config.php';

// A completed installation requires both private configuration and the lock.
$installLock = APP_ROOT . '/runtime/installed.lock';
if (!is_file($installLock)) {
    if (is_file($configPath)) {
        http_response_code(503);
        exit('Installation is incomplete. Repair configuration privately.');
    }
    header('Location: /install.php', true, 303);
    exit;
}
if (!is_file($configPath)) {
    http_response_code(503);
    exit('Installation configuration is missing.');
}

$config = require $configPath;

if (!is_array($config)) {
    http_response_code(503);
    exit('Deployment Manager configuration is invalid.');
}

if (!is_dir(RUNTIME_DIR) || !is_writable(RUNTIME_DIR)) {
    http_response_code(503);
    exit('Deployment Manager runtime directory is not writable.');
}

// Never allow a login session over plain HTTP.
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    http_response_code(403);
    exit('HTTPS is required.');
}

ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

session_name('kcdf_deploy_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');

/** Check authentication and expire sessions after inactivity. */
function isLoggedIn(): bool
{
    global $config;

    if (empty($_SESSION['authenticated'])) {
        return false;
    }

    $timeout = (int) ($config['session_timeout_seconds'] ?? 1800);
    $lastSeen = (int) ($_SESSION['last_seen'] ?? 0);

    if (time() - $lastSeen > $timeout) {
        $_SESSION = [];
        return false;
    }

    $_SESSION['last_seen'] = time();
    return true;
}

/** Redirect visitors who have not logged in. */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: /login.php', true, 303);
        exit;
    }
}

/** Create (or return) the CSRF token tied to this session. */
function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/** Reject state-changing requests without a valid CSRF token. */
function verifyCsrfToken(): void
{
    $submittedToken = $_POST['csrf_token'] ?? null;

    if (!is_string($submittedToken) || !hash_equals(csrfToken(), $submittedToken)) {
        http_response_code(403);
        exit('Invalid CSRF token.');
    }
}

/** HTML-escape all values written into a page. */
function escapeHtml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read the current deployment status, or provide a useful initial state. */
function readDeploymentStatus(): array
{
    $path = RUNTIME_DIR . '/status.json';

    if (!is_file($path)) {
        return ['status' => 'idle', 'step' => 'No deployment yet'];
    }

    $data = json_decode((string) file_get_contents($path), true);

    return is_array($data)
        ? $data
        : ['status' => 'unknown', 'step' => 'Unable to read deployment status'];
}

/**
 * Write JSON via a temporary file and rename it atomically.
 *
 * The caller is responsible for locking when multiple processes may update
 * the same state. The temporary file lives beside the target for atomic rename.
 */
function writeJsonAtomically(string $path, array $data): void
{
    $temporaryPath = tempnam(dirname($path), '.tmp-');

    if ($temporaryPath === false) {
        throw new RuntimeException('Unable to create temporary state file.');
    }

    try {
        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );

        if (file_put_contents($temporaryPath, $json . "\n") === false) {
            throw new RuntimeException('Unable to write deployment state.');
        }

        chmod($temporaryPath, 0600);

        if (!rename($temporaryPath, $path)) {
            throw new RuntimeException('Unable to replace deployment state.');
        }
    } finally {
        if (is_file($temporaryPath)) {
            unlink($temporaryPath);
        }
    }
}
