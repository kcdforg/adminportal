<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$storagePath = $root . '/storage';
$lockPath = $storagePath . '/installed.lock';
$envPath = $root . '/.env';

session_set_cookie_params([
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Strict',
]);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
$scriptNonce = base64_encode(random_bytes(18));
header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; script-src 'nonce-{$scriptNonce}'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
header('X-Frame-Options: DENY');

$installed = is_file($lockPath);
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && $_SERVER['HTTPS'] !== 'off';
$isLoopback = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
$secureRequest = $isHttps || $isLoopback;
$errors = [];
$success = isset($_GET['installed']) && $_GET['installed'] === '1' && $installed;
$input = [
    'db_mode' => 'new',
    'db_host' => '127.0.0.1',
    'db_port' => '3306',
    'db_name' => 'kcdf_parents',
    'db_user' => 'root',
    'cors_origins' => 'http://localhost:4200,http://localhost:8100',
    'admin_username' => '',
    'admin_email' => '',
    'user_username' => '',
    'user_email' => '',
];

if (!$installed && is_file($envPath)) {
    $errors[] = 'A .env configuration already exists. The installer will not overwrite it.';
}

$requirements = installerRequirements($root);
$requirementsMet = count(array_filter($requirements, static fn (array $check): bool => !$check['ok'])) === 0;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !$installed && $requirementsMet && $errors === []) {
    set_time_limit(300);

    if (!$secureRequest) {
        $errors[] = 'Installer credentials may only be submitted over HTTPS or from the local machine.';
    }
    foreach ($input as $key => $_) {
        $input[$key] = trim(installerPostString($key));
    }

    if (!hash_equals((string) ($_SESSION['installer_csrf'] ?? ''), installerPostString('csrf_token'))) {
        $errors[] = 'Your session expired. Reload the page and try again.';
    }
    if (!in_array($input['db_mode'], ['new', 'existing'], true)) {
        $errors[] = 'Choose whether to create a new database or connect to an existing API database.';
    }
    $port = filter_var(installerPostString('db_port'), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 65535],
    ]);
    if (!installerIsValidHost($input['db_host'])) {
        $errors[] = 'Enter a valid database host name or IP address.';
    }
    if ($port === false) {
        $errors[] = 'Database port must be between 1 and 65535.';
    }
    if (preg_match('/^[A-Za-z0-9_]{1,64}$/', $input['db_name']) !== 1) {
        $errors[] = 'Database name may contain only letters, numbers, and underscores (up to 64 characters).';
    }
    if ($input['db_user'] === '' || strlen($input['db_user']) > 128 || preg_match('/[\x00-\x1F\x7F]/', $input['db_user'])) {
        $errors[] = 'Enter a valid database username.';
    }

    $origins = installerNormalizeOrigins($input['cors_origins']);
    if ($origins === null) {
        $errors[] = 'Enter one or more valid HTTP/HTTPS origins, separated by commas (for example https://portal.example.org).';
    }

    $username = trim(installerPostString('admin_username'));
    $email = trim(installerPostString('admin_email'));
    $password = installerPostString('admin_password');
    $passwordConfirm = installerPostString('admin_password_confirm');
    $userUsername = trim(installerPostString('user_username'));
    $userEmail = trim(installerPostString('user_email'));
    $userPassword = installerPostString('user_password');
    $userPasswordConfirm = installerPostString('user_password_confirm');

    if ($input['db_mode'] === 'new') {
        if ($username === '' || strlen($username) > 100 || preg_match('/[\x00-\x1F\x7F]/', $username)) {
            $errors[] = 'Admin username is required and must be at most 100 characters.';
        }
        if (strlen($email) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Enter a valid admin email address (up to 255 characters).';
        }
        if ($password === '') {
            $errors[] = 'Admin password is required.';
        }
        if (str_contains($password, "\0") || str_contains(installerPostString('db_password'), "\0")) {
            $errors[] = 'Passwords must not contain a null byte.';
        }
        if (!hash_equals($password, $passwordConfirm)) {
            $errors[] = 'Admin password confirmation does not match.';
        }

        if ($userUsername === '' || strlen($userUsername) > 100 || preg_match('/[\x00-\x1F\x7F]/', $userUsername)) {
            $errors[] = 'User username is required and must be at most 100 characters.';
        }
        if (strlen($userEmail) > 255 || filter_var($userEmail, FILTER_VALIDATE_EMAIL) === false) {
            $errors[] = 'Enter a valid user email address (up to 255 characters).';
        }
        if ($userPassword === '') {
            $errors[] = 'User password is required.';
        }
        if (str_contains($userPassword, "\0")) {
            $errors[] = 'Passwords must not contain a null byte.';
        }
        if (!hash_equals($userPassword, $userPasswordConfirm)) {
            $errors[] = 'User password confirmation does not match.';
        }
        if (strcasecmp($username, $userUsername) === 0) {
            $errors[] = 'Admin and user accounts must have different usernames.';
        }
    }

    if ($errors === []) {
        $pdo = null;
        $createdDatabase = false;
        $createdEnv = false;
        $createdLock = false;

        try {
            ensureInstallerStorage($storagePath);
            $pdo = installerConnectServer(
                $input['db_host'],
                (int) $port,
                $input['db_user'],
                installerPostString('db_password')
            );

            $databaseExists = $pdo->prepare(
                'SELECT 1 FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = :database'
            );
            $databaseExists->execute([':database' => $input['db_name']]);
            $databaseAlreadyExists = $databaseExists->fetchColumn() !== false;
            if ($input['db_mode'] === 'new') {
                if ($databaseAlreadyExists) {
                    throw new DomainException(
                        'That database already exists. Choose a new, empty database name; existing databases are never modified.'
                    );
                }

                $databaseName = '`' . $input['db_name'] . '`';
                $pdo->exec("CREATE DATABASE {$databaseName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $createdDatabase = true;
                $pdo->exec("USE {$databaseName}");
                installerImportSchema($pdo, $root . '/database/schema.sql');
                installerCreateInitialAccounts(
                    $pdo,
                    [
                        'first_name' => 'Admin',
                        'last_name' => 'Account',
                        'username' => $username,
                        'email' => $email,
                        'password' => $password,
                    ],
                    [
                        'first_name' => 'Member',
                        'last_name' => 'Account',
                        'username' => $userUsername,
                        'email' => $userEmail,
                        'password' => $userPassword,
                    ]
                );
            } else {
                if (!$databaseAlreadyExists) {
                    throw new DomainException('The selected existing database does not exist.');
                }
                $pdo->exec('USE `' . $input['db_name'] . '`');
                installerVerifyExistingSchema($pdo, $root . '/database/schema.sql');
            }

            $jwtSecret = bin2hex(random_bytes(64));
            $environment = installerEnvironment([
                'APP_ENV' => 'production',
                'APP_DEBUG' => 'false',
                'DB_TYPE' => 'mysql',
                'DB_HOST' => $input['db_host'],
                'DB_PORT' => (string) $port,
                'DB_DATABASE' => $input['db_name'],
                'DB_USERNAME' => $input['db_user'],
                'DB_PASSWORD' => installerPostString('db_password'),
                'JWT_SECRET' => $jwtSecret,
                'JWT_ALGORITHM' => 'HS256',
                'JWT_ACCESS_TTL' => '900',
                'JWT_REFRESH_TTL' => '2592000',
                'CORS_ALLOWED_ORIGINS' => implode(',', $origins),
                'LOG_LEVEL' => 'warning',
            ]);
            installerWriteNewFile($envPath, $environment, 0640);
            $createdEnv = true;
            installerWriteNewFile($lockPath, json_encode([
                'installed_at' => date(DATE_ATOM),
                'version' => '1.0.0',
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL, 0640);
            $createdLock = true;

            session_regenerate_id(true);
            $_SESSION = [];
            header('Location: ?installed=1', true, 303);
            exit;
        } catch (DomainException $exception) {
            $errors[] = $exception->getMessage();
        } catch (Throwable $exception) {
            error_log('API web installer failed: ' . $exception->getMessage());
            $errors[] = 'Installation failed. Check the PHP/server error log. Any database created by this attempt will be removed if setup did not complete.';
        } finally {
            if (!$createdLock) {
                if ($createdEnv && is_file($envPath)) {
                    if (!unlink($envPath)) {
                        error_log('API web installer could not remove its temporary environment file.');
                        $errors[] = 'The temporary .env file could not be removed after failure. Restrict access to it and remove it before retrying.';
                    }
                }
                if ($createdDatabase && $pdo instanceof PDO) {
                    try {
                        $pdo->exec('DROP DATABASE `' . $input['db_name'] . '`');
                    } catch (Throwable $cleanupException) {
                        error_log('API web installer database cleanup failed: ' . $cleanupException->getMessage());
                        $errors[] = 'The new database could not be removed automatically after the failed installation. Do not use it; remove it manually only after verifying its contents.';
                    }
                }
                if ($createdLock && is_file($lockPath)) {
                    unlink($lockPath);
                }
            }
        }
    }
}

if (empty($_SESSION['installer_csrf'])) {
    $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
}

function installerRequirements(string $root): array
{
    $storagePath = $root . '/storage';
    $checks = [
        'php' => ['PHP 8.1 or newer', PHP_VERSION_ID >= 80100, 'Current version: ' . PHP_VERSION],
        'pdo' => ['PDO extension', extension_loaded('pdo'), extension_loaded('pdo') ? 'Available' : 'Missing'],
        'mysql' => ['PDO MySQL driver', extension_loaded('pdo_mysql'), extension_loaded('pdo_mysql') ? 'Available' : 'Missing'],
        'json' => ['JSON extension', extension_loaded('json'), extension_loaded('json') ? 'Available' : 'Missing'],
        'hash' => ['Hash extension', extension_loaded('hash'), extension_loaded('hash') ? 'Available' : 'Missing'],
        'openssl' => ['OpenSSL extension', extension_loaded('openssl'), extension_loaded('openssl') ? 'Available' : 'Missing'],
        'schema' => ['Bundled MySQL schema', is_file($root . '/database/schema.sql'), 'database/schema.sql'],
        'root_writable' => ['Backend directory writable', is_writable($root), is_writable($root) ? 'Ready to write .env' : 'Not writable'],
        'storage_writable' => [
            'Storage directory writable or creatable',
            is_dir($storagePath) ? is_writable($storagePath) : is_writable($root),
            is_dir($storagePath) ? (is_writable($storagePath) ? 'Writable' : 'Not writable') : 'Will be created during installation',
        ],
    ];

    $result = [];
    foreach ($checks as $key => [$label, $ok, $message]) {
        $result[$key] = ['label' => $label, 'ok' => $ok, 'message' => $message];
    }
    return $result;
}

function installerNormalizeOrigins(string $value): ?array
{
    $parts = array_values(array_filter(array_map('trim', explode(',', $value)), static fn (string $part): bool => $part !== ''));
    if ($parts === []) {
        return null;
    }

    $origins = [];
    foreach ($parts as $origin) {
        $parts = parse_url($origin);
        if (!is_array($parts)
            || !in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['query'])
            || isset($parts['fragment'])
            || (isset($parts['path']) && $parts['path'] !== '' && $parts['path'] !== '/')
            || filter_var($origin, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        $normalized = strtolower((string) $parts['scheme']) . '://' . strtolower((string) $parts['host']);
        if (isset($parts['port'])) {
            if ($parts['port'] < 1 || $parts['port'] > 65535) {
                return null;
            }
            $normalized .= ':' . $parts['port'];
        }
        $origins[] = $normalized;
    }

    return array_values(array_unique($origins));
}

function installerIsValidHost(string $host): bool
{
    if (filter_var($host, FILTER_VALIDATE_IP) !== false) {
        return true;
    }

    return preg_match(
        '/^(?=.{1,253}$)(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?)(?:\.(?:[A-Za-z0-9](?:[A-Za-z0-9-]{0,61}[A-Za-z0-9])?))*$/D',
        $host
    ) === 1;
}

function installerPostString(string $key): string
{
    $value = $_POST[$key] ?? '';
    return is_string($value) ? $value : '';
}

function ensureInstallerStorage(string $storagePath): void
{
    if (is_link($storagePath)) {
        throw new RuntimeException('Storage path must not be a symbolic link.');
    }
    if (!is_dir($storagePath) && !mkdir($storagePath, 0750) && !is_dir($storagePath)) {
        throw new RuntimeException('Could not create the storage directory.');
    }
    if (!is_writable($storagePath)) {
        throw new RuntimeException('Storage directory is not writable.');
    }
    $logsPath = $storagePath . '/logs';
    if (is_link($logsPath)) {
        throw new RuntimeException('Log path must not be a symbolic link.');
    }
    if (!is_dir($logsPath) && !mkdir($logsPath, 0750) && !is_dir($logsPath)) {
        throw new RuntimeException('Could not create the log directory.');
    }
    if (!is_writable($logsPath)) {
        throw new RuntimeException('Log directory is not writable.');
    }
}

function installerConnectServer(string $host, int $port, string $username, string $password): PDO
{
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%d;charset=utf8mb4', $host, $port),
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    $pdo->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
    return $pdo;
}

function installerImportSchema(PDO $pdo, string $schemaPath): void
{
    $schema = file_get_contents($schemaPath);
    if ($schema === false || preg_match('/^\s*DROP\s+TABLE\b/im', $schema)) {
        throw new RuntimeException('The schema is missing or contains prohibited destructive DROP TABLE statements.');
    }

    $expectedTables = [];
    preg_match_all('/^\s*CREATE\s+TABLE\s+`([^`]+)`/im', $schema, $matches);
    $expectedTables = $matches[1] ?? [];
    if ($expectedTables === []) {
        throw new RuntimeException('The schema contains no CREATE TABLE statements.');
    }

    $schema = preg_replace('/^\s*--.*$/m', '', $schema) ?? $schema;
    $statements = preg_split('/;\s*(?=\r?\n|$)/', $schema) ?: [];
    try {
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }
    } finally {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    }

    $placeholders = implode(',', array_fill(0, count($expectedTables), '?'));
    $query = $pdo->prepare(
        'SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN (' . $placeholders . ')'
    );
    $query->execute($expectedTables);
    $createdTables = $query->fetchAll(PDO::FETCH_COLUMN);
    sort($expectedTables);
    sort($createdTables);
    if ($expectedTables !== $createdTables) {
        throw new RuntimeException('Schema verification failed; not all expected database tables were created.');
    }
}

function installerVerifyExistingSchema(PDO $pdo, string $schemaPath): void
{
    $schema = file_get_contents($schemaPath);
    if ($schema === false) {
        throw new RuntimeException('The bundled API schema could not be read.');
    }

    preg_match_all('/^\s*CREATE\s+TABLE\s+`([^`]+)`/im', $schema, $matches);
    $expectedTables = array_values(array_unique($matches[1] ?? []));
    if ($expectedTables === []) {
        throw new RuntimeException('The bundled API schema contains no table definitions.');
    }

    $placeholders = implode(',', array_fill(0, count($expectedTables), '?'));
    $query = $pdo->prepare(
        'SELECT TABLE_NAME FROM information_schema.TABLES ' .
        'WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN (' . $placeholders . ')'
    );
    $query->execute($expectedTables);
    $existingTables = $query->fetchAll(PDO::FETCH_COLUMN);
    $missingTables = array_values(array_diff($expectedTables, $existingTables));
    if ($missingTables !== []) {
        throw new DomainException(
            'The existing database is missing required API tables: ' . implode(', ', $missingTables) .
            '. No schema or account changes were made.'
        );
    }
}

function installerCreateInitialAccounts(PDO $pdo, array $adminAccount, array $userAccount): void
{
    $pdo->beginTransaction();
    try {
        $createLogin = static function (array $account) use ($pdo): int {
            $profile = $pdo->prepare(
                'INSERT INTO member_profiles (first_name, last_name, email, status) VALUES (:first_name, :last_name, :email, :status)'
            );
            $profile->execute([
                ':first_name' => $account['first_name'],
                ':last_name' => $account['last_name'],
                ':email' => $account['email'],
                ':status' => 'active',
            ]);
            $profileId = (int) $pdo->lastInsertId();

            $login = $pdo->prepare(
                'INSERT INTO user_logins (profile_id, username, password_hash, is_active) VALUES (:profile_id, :username, :password_hash, 1)'
            );
            $login->execute([
                ':profile_id' => $profileId,
                ':username' => $account['username'],
                ':password_hash' => password_hash($account['password'], PASSWORD_DEFAULT),
            ]);

            return $profileId;
        };

        $adminProfileId = $createLogin($adminAccount);
        $createLogin($userAccount);

        $admin = $pdo->prepare(
            'INSERT INTO admins (profile_id, admin_role, status) VALUES (:profile_id, :admin_role, :status)'
        );
        $admin->execute([
            ':profile_id' => $adminProfileId,
            ':admin_role' => 'super_admin',
            ':status' => 'active',
        ]);
        $pdo->commit();
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

function installerEnvironment(array $values): string
{
    $lines = [];
    foreach ($values as $key => $value) {
        $lines[] = $key . '=' . json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
    return implode(PHP_EOL, $lines) . PHP_EOL;
}

function installerWriteNewFile(string $path, string $contents, int $permissions): void
{
    $handle = @fopen($path, 'x');
    if ($handle === false) {
        throw new RuntimeException('Could not create a required installation file.');
    }

    try {
        $length = strlen($contents);
        $written = 0;
        while ($written < $length) {
            $bytes = fwrite($handle, substr($contents, $written));
            if ($bytes === false || $bytes === 0) {
                throw new RuntimeException('Could not write a required installation file.');
            }
            $written += $bytes;
        }
        if (!fflush($handle) || !chmod($path, $permissions)) {
            throw new RuntimeException('Could not write a required installation file.');
        }
    } catch (Throwable $exception) {
        fclose($handle);
        @unlink($path);
        throw $exception;
    }
    fclose($handle);
}

function installerEscape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$csrf = (string) $_SESSION['installer_csrf'];
$post = static fn (string $key): string => installerEscape(installerPostString($key));
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KCDF Parents API installer</title>
    <style>
        :root { color-scheme: light; font: 16px/1.5 system-ui, sans-serif; color: #172033; background: #f3f6fb; }
        body { margin: 0; padding: 2rem 1rem; }
        main { max-width: 760px; margin: 0 auto; }
        section, fieldset { background: #fff; border: 1px solid #dbe3ef; border-radius: 12px; padding: 1.5rem; margin: 1rem 0; }
        fieldset { min-width: 0; }
        h1, h2 { line-height: 1.2; } h1 { margin-top: 0; }
        label { display: block; font-weight: 650; margin: .9rem 0 .25rem; }
        input { box-sizing: border-box; width: 100%; padding: .65rem .75rem; border: 1px solid #aab7c9; border-radius: 6px; font: inherit; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 1rem; }
        .check { display: flex; align-items: start; gap: .65rem; padding: .4rem 0; border-bottom: 1px solid #edf0f5; }
        .check input, .confirm input { width: auto; margin-top: .35rem; }
        .status { margin-left: auto; font-size: .9rem; color: #506078; text-align: right; }
        .ok { color: #137044; } .fail, .error { color: #a32626; }
        .errorbox { background: #fff0f0; border: 1px solid #f1b7b7; border-radius: 6px; padding: .75rem 1rem; }
        .success { background: #edf9f1; border: 1px solid #a8d9b8; border-radius: 6px; padding: 1rem; }
        .hint { color: #526078; font-size: .92rem; }
        button { border: 0; border-radius: 6px; padding: .75rem 1rem; color: #fff; background: #1459a6; font: inherit; font-weight: 650; cursor: pointer; }
        button:disabled { background: #8793a4; cursor: not-allowed; }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; } section { padding: 1rem; } }
    </style>
</head>
<body>
<main>
    <h1>KCDF Parents API setup</h1>
    <p class="hint">This one-time installer configures the API to use MySQL. Choose whether to create a new database with initial accounts or connect to an existing API database without modifying it.</p>

    <?php if ($installed || $success): ?>
        <section class="success">
            <h2>Installation is locked</h2>
            <p>The API setup is complete. The browser installer will not run again while <code>storage/installed.lock</code> exists.</p>
            <p>Remove or restrict access to the <code>install/</code> directory before exposing the API publicly.</p>
        </section>
    <?php else: ?>
        <section>
            <h2>Server requirements</h2>
            <?php foreach ($requirements as $check): ?>
                <div class="check">
                    <span class="<?= $check['ok'] ? 'ok' : 'fail' ?>"><?= $check['ok'] ? 'OK' : 'Missing' ?></span>
                    <strong><?= installerEscape($check['label']) ?></strong>
                    <span class="status"><?= installerEscape($check['message']) ?></span>
                </div>
            <?php endforeach; ?>
        </section>

        <?php if (!$secureRequest): ?>
            <p class="errorbox error">Use HTTPS to install remotely. HTTP setup is allowed only from the local machine.</p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p class="errorbox error"><?= installerEscape($error) ?></p>
        <?php endforeach; ?>

        <?php if ($requirementsMet && !is_file($envPath)): ?>
            <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= installerEscape($csrf) ?>">
                <section>
                    <h2>Database setup</h2>
                    <label for="db_mode">Database option</label>
                    <select id="db_mode" name="db_mode" required>
                        <option value="new" <?= $input['db_mode'] === 'new' ? 'selected' : '' ?>>Create a new database and initial accounts</option>
                        <option value="existing" <?= $input['db_mode'] === 'existing' ? 'selected' : '' ?>>Connect to an existing API database (no schema or account changes)</option>
                    </select>
                    <p class="hint">Use a MySQL account permitted to connect to the selected database. Creating a new database also requires permission to create databases and tables.</p>
                    <div class="grid">
                        <div><label for="db_host">Database host</label><input id="db_host" name="db_host" required value="<?= $post('db_host') ?: '127.0.0.1' ?>"></div>
                        <div><label for="db_port">Port</label><input id="db_port" name="db_port" type="number" min="1" max="65535" required value="<?= $post('db_port') ?: '3306' ?>"></div>
                    </div>
                    <label for="db_name">Database name</label><input id="db_name" name="db_name" required maxlength="64" pattern="[A-Za-z0-9_]+" value="<?= $post('db_name') ?: 'kcdf_parents' ?>">
                    <label for="db_user">Database username</label><input id="db_user" name="db_user" required maxlength="128" autocomplete="username" value="<?= $post('db_user') ?: 'root' ?>">
                    <label for="db_password">Database password</label><input id="db_password" name="db_password" type="password" autocomplete="new-password">
                    <label for="cors_origins">Allowed frontend origins</label><input id="cors_origins" name="cors_origins" required value="<?= $post('cors_origins') ?: 'http://localhost:4200,http://localhost:8100' ?>">
                    <p class="hint">Comma-separated exact origins, including scheme and optional port. No paths or wildcard origins.</p>
                    <p id="existing-database-note" class="hint" hidden>The existing database must already have the API schema. The installer verifies its tables and writes connection settings only; it does not change the database or create accounts.</p>
                </section>

                <fieldset id="initial-accounts">
                    <legend>Initial accounts for new database</legend>
                    <p class="hint">First and last names are assigned automatically as “Admin Account” and “Member Account.” Passwords may be any non-empty value. Choose strong passwords for real deployments.</p>
                    <section>
                        <h2>Initial super admin</h2>
                        <label for="admin_username">Login username</label><input id="admin_username" name="admin_username" required maxlength="100" autocomplete="username" value="<?= $post('admin_username') ?>">
                        <label for="admin_email">Email</label><input id="admin_email" name="admin_email" type="email" required maxlength="255" autocomplete="email" value="<?= $post('admin_email') ?>">
                        <div class="grid">
                            <div><label for="admin_password">Password</label><input id="admin_password" name="admin_password" type="password" required autocomplete="new-password"></div>
                            <div><label for="admin_password_confirm">Confirm password</label><input id="admin_password_confirm" name="admin_password_confirm" type="password" required autocomplete="new-password"></div>
                        </div>
                    </section>
                    <section>
                        <h2>Initial member account</h2>
                        <p class="hint">This creates a member profile and its login credentials directly in the database. The Members API creates profiles only; it does not create login accounts. This account has no admin role or family membership.</p>
                        <label for="user_username">Login username</label><input id="user_username" name="user_username" required maxlength="100" autocomplete="username" value="<?= $post('user_username') ?>">
                        <label for="user_email">Email</label><input id="user_email" name="user_email" type="email" required maxlength="255" autocomplete="email" value="<?= $post('user_email') ?>">
                        <div class="grid">
                            <div><label for="user_password">Password</label><input id="user_password" name="user_password" type="password" required autocomplete="new-password"></div>
                            <div><label for="user_password_confirm">Confirm password</label><input id="user_password_confirm" name="user_password_confirm" type="password" required autocomplete="new-password"></div>
                        </div>
                    </section>
                </fieldset>
                <button id="install-submit" type="submit" <?= $requirementsMet && $secureRequest ? '' : 'disabled' ?>>Install API</button>
            </form>
            <script nonce="<?= installerEscape($scriptNonce) ?>">
                const databaseMode = document.getElementById("db_mode");
                const accountFields = document.getElementById("initial-accounts");
                const existingDatabaseNote = document.getElementById("existing-database-note");
                const submitButton = document.getElementById("install-submit");

                function updateDatabaseMode() {
                    const isNewDatabase = databaseMode.value === "new";
                    accountFields.disabled = !isNewDatabase;
                    accountFields.hidden = !isNewDatabase;
                    existingDatabaseNote.hidden = isNewDatabase;
                    submitButton.textContent = isNewDatabase ? "Create database and install API" : "Configure existing database";
                }

                databaseMode.addEventListener("change", updateDatabaseMode);
                updateDatabaseMode();
            </script>
        <?php elseif (!$requirementsMet): ?>
            <p class="hint">Resolve the server requirement failures before installing.</p>
        <?php endif; ?>
    <?php endif; ?>
</main>
</body>
</html>
