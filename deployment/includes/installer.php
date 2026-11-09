<?php

declare(strict_types=1);

/** Shared setup checks and safe configuration serialization. */
const INSTALL_ROOT = __DIR__ . '/..';
const INSTALL_LOCK = INSTALL_ROOT . '/runtime/installed.lock';
const ADMIN_CONFIG = INSTALL_ROOT . '/config/config.php';
const DEPLOY_CONFIG = INSTALL_ROOT . '/config/deploy.env';

function installerInstalled(): bool
{
    return is_file(INSTALL_LOCK) || is_file(ADMIN_CONFIG) || is_file(DEPLOY_CONFIG);
}

function installerEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function installerSetupSecret(): string
{
    // Provision this once in HestiaCP's PHP environment or in a private file.
    $secret = getenv('KCDF_DEPLOY_SETUP_SECRET');
    if (is_string($secret) && strlen($secret) >= 32) {
        return trim($secret);
    }
    $path = INSTALL_ROOT . '/config/setup-secret.txt';
    if (is_file($path)) {
        $secret = trim((string) file_get_contents($path));
        if (strlen($secret) >= 32) {
            return $secret;
        }
    }
    return '';
}

function installerValidate(array $input): array
{
    $errors = [];
    $fields = ['username', 'password', 'confirm_password', 'repo_url', 'branch',
        'repo_dir', 'api_source', 'admin_source', 'member_source',
        'api_dest', 'admin_dest', 'member_dest'];
    foreach ($fields as $field) {
        if (!is_string($input[$field] ?? null) || trim($input[$field]) === '') {
            $errors[] = "Required field: $field";
        }
    }
    if ($errors) return $errors;

    if (!preg_match('/^[a-zA-Z0-9_.-]{3,64}$/', $input['username'])) {
        $errors[] = 'Username must contain 3–64 letters, numbers, dots, dashes or underscores.';
    }
    if (strlen($input['password']) < 12) {
        $errors[] = 'Password must be at least 12 characters.';
    }
    if (!hash_equals($input['password'], $input['confirm_password'])) {
        $errors[] = 'Passwords do not match.';
    }
    if (!preg_match('/^[a-zA-Z0-9._\/-]+$/', $input['branch']) || str_starts_with($input['branch'], '-')) {
        $errors[] = 'Git branch contains unsupported characters.';
    }
    if (!preg_match('~^(git@[^\s]+:[^\s]+|https://[^\s]+)$~', $input['repo_url'])) {
        $errors[] = 'Repository URL must be an SSH Git URL or HTTPS URL.';
    }
    foreach (['repo_dir', 'api_dest', 'admin_dest', 'member_dest'] as $field) {
        $path = $input[$field];
        if (!str_starts_with($path, '/') || preg_match('/[\r\n\x00"\x27`$\\]/', $path)
            || str_contains($path, '/../') || str_ends_with($path, '/..')) {
            $errors[] = "Invalid absolute path: $field";
        }
    }
    foreach (['api_source', 'admin_source', 'member_source'] as $field) {
        $path = $input[$field];
        if (str_starts_with($path, '/') || str_contains($path, '..')
            || !preg_match('~^[a-zA-Z0-9_./-]+$~', $path)) {
            $errors[] = "Invalid repository-relative path: $field";
        }
    }
    foreach (['api_dest', 'admin_dest', 'member_dest'] as $field) {
        $path = $input[$field];
        if (!is_dir($path) || is_link($path) || !is_writable($path)) {
            $errors[] = "Destination must exist, be a real directory, and be writable: $field";
        } elseif (!preg_match('~^/home/[^/]+/web/[^/]+/public_html$~', realpath($path) ?: '')) {
            $errors[] = "Destination is not a HestiaCP public_html path: $field";
        }
    }
    foreach (['api_dest', 'admin_dest', 'member_dest'] as $field) {
        $destination = rtrim($input[$field], '/');
        $checkout = rtrim($input['repo_dir'], '/');
        if ($destination === $checkout || str_starts_with($destination . '/', $checkout . '/')) {
            $errors[] = 'The Git checkout must not contain a deployment destination.';
        }
    }
    if (count(array_unique([$input['api_dest'], $input['admin_dest'], $input['member_dest']])) !== 3) {
        $errors[] = 'Application destinations must be different.';
    }
    foreach (['git', 'rsync', 'flock', 'realpath', 'nohup'] as $command) {
        // The installer does not execute user-supplied shell commands.
        if (!is_executable('/usr/bin/' . $command) && !is_executable('/bin/' . $command)) {
            $errors[] = "Required command not found at standard path: $command";
        }
    }
    return $errors;
}

function installerShellValue(string $value): string
{
    // POSIX single-quoted shell assignment; prevents command substitution.
    return "'" . str_replace("'", "'\\''", $value) . "'";
}

function installerWriteExclusive(string $path, string $content): void
{
    $handle = fopen($path, 'x');
    if ($handle === false) throw new RuntimeException('Could not create ' . basename($path));
    chmod($path, 0600);
    try {
        if (fwrite($handle, $content) !== strlen($content) || !fflush($handle)) {
            throw new RuntimeException('Could not finish writing ' . basename($path));
        }
    } finally {
        fclose($handle);
    }
}

function installerComplete(array $input): void
{
    if (installerInstalled()) throw new RuntimeException('Installation is already configured.');
    $admin = [
        'username' => $input['username'],
        'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
        'session_timeout_seconds' => 1800,
    ];
    $php = "<?php\n\ndeclare(strict_types=1);\n\nreturn " . var_export($admin, true) . ";\n";
    $env = "# Generated by the first-run installer. Keep private.\n";
    $map = [
        'REPO_URL' => 'repo_url', 'BRANCH' => 'branch', 'REPO_DIR' => 'repo_dir',
        'API_SOURCE' => 'api_source', 'ADMIN_SOURCE' => 'admin_source',
        'MEMBER_SOURCE' => 'member_source', 'API_DEST' => 'api_dest',
        'ADMIN_DEST' => 'admin_dest', 'MEMBER_DEST' => 'member_dest',
    ];
    foreach ($map as $key => $field) {
        $env .= $key . '=' . installerShellValue(trim($input[$field])) . "\n";
    }
    $env .= "DRY_RUN=1\n";

    // A partial write deliberately blocks public re-installation. Repair privately.
    installerWriteExclusive(ADMIN_CONFIG, $php);
    installerWriteExclusive(DEPLOY_CONFIG, $env);
    installerWriteExclusive(INSTALL_LOCK, "Installed at " . gmdate('c') . "\n");
    @unlink(INSTALL_ROOT . '/config/setup-secret.txt');
}
