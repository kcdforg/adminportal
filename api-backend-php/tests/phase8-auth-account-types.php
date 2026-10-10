<?php

declare(strict_types=1);

$root = dirname(__DIR__);
require $root . '/config/autoload.php';
require $root . '/services/auth.php';
require $root . '/services/identity.php';

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "phase-8 checks require PDO_SQLITE\n");
    exit(2);
}

putenv('JWT_SECRET=phase8-auth-account-types-test-secret-123456789');

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$assert(
    getUserRoles([
        'portal' => 'admin',
        'user_type' => 'admin',
        'roles' => ['admin_super', 'family_primary'],
    ]) === ['admin_super'],
    'Admin tokens must contain only admin portal roles.'
);
$assert(
    getUserRoles([
        'portal' => 'member',
        'user_type' => 'member',
        'roles' => ['admin_super', 'family_primary'],
    ]) === ['family_primary'],
    'Member tokens must not retain admin roles.'
);
$assert(
    getUserRoles([
        'portal' => 'member',
        'user_type' => 'admin',
        'roles' => ['admin_super'],
    ]) === [],
    'A token with mismatched portal and account types must have no roles.'
);

$schema = file_get_contents($root . '/database/schema.sql');
$assert(is_string($schema), 'The main database schema must be readable.');
$assert(
    str_contains($schema, "`user_type`     ENUM('admin', 'member')")
        && str_contains($schema, "`role`          ENUM('super_admin', 'program_manager', 'accounts', 'readonly')")
        && str_contains($schema, '`profile_id`    BIGINT UNSIGNED NULL')
        && str_contains($schema, '`display_name`  VARCHAR(150) NULL')
        && str_contains($schema, 'UNIQUE KEY `uq_user_logins_profile_id` (`profile_id`)')
        && str_contains($schema, '`login_id`   BIGINT UNSIGNED NOT NULL')
        && !str_contains($schema, 'CREATE TABLE `admins`'),
    'The main schema must support profile-less admin logins and login-keyed refresh tokens without an admins table.'
);
$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
$database = new Medoo\Medoo(['pdo' => $pdo, 'type' => 'sqlite', 'database' => ':memory:']);
$assert(
    resolveLoginDisplayName($database, null, 'standalone.admin', 'Standalone Admin') === 'Standalone Admin',
    'An explicit login display name must take precedence over the username.'
);
$pdo->exec('CREATE TABLE refresh_tokens (id INTEGER PRIMARY KEY AUTOINCREMENT, login_id INTEGER NOT NULL, profile_id INTEGER NULL, token_hash TEXT NOT NULL, expires_at TEXT NOT NULL, created_at TEXT NOT NULL)');
$pdo->exec("CREATE TABLE user_logins (id INTEGER PRIMARY KEY, profile_id INTEGER NULL, username TEXT NOT NULL, user_type TEXT NOT NULL, role TEXT NULL, last_login_at TEXT NULL)");
$bootstrap = ['config' => ['jwt' => ['access_ttl' => 900, 'refresh_ttl' => 3600]]];
$login = [
    'id' => 22,
    'profile_id' => 7,
    'username' => 'admin.test',
    'display_name' => 'Admin Tester',
    'user_type' => 'admin',
    'role' => 'super_admin',
];
$tokens = issueLoginTokens($database, $bootstrap, $login, [
    'first_name' => 'Admin',
    'last_name' => 'Tester',
    'email' => 'admin@example.test',
], false);
$accessClaims = decodeJwtToken($tokens['access_token']);
$refreshClaims = decodeJwtToken($tokens['refresh_token'], true);
$assert(
    $accessClaims['portal'] === 'admin'
        && $accessClaims['user_type'] === 'admin'
        && $accessClaims['login_id'] === 22
        && $accessClaims['roles'] === ['admin_super'],
    'Admin access tokens must carry the correct login, portal, and role.'
);
$assert(
    $refreshClaims['type'] === 'refresh'
        && $refreshClaims['portal'] === 'admin'
        && $refreshClaims['user_type'] === 'admin'
        && $refreshClaims['login_id'] === 22,
    'Admin refresh tokens must preserve their portal scope.'
);
$standaloneLogin = $login;
$standaloneLogin['id'] = 23;
$standaloneLogin['profile_id'] = null;
$standaloneLogin['username'] = 'standalone.admin';
$standaloneLogin['display_name'] = 'Standalone Admin';
$tokens = issueLoginTokens($database, $bootstrap, $standaloneLogin, null, false);
$accessClaims = decodeJwtToken($tokens['access_token']);
$assert(
    $accessClaims['profile_id'] === 0
        && $accessClaims['login_id'] === 23
        && $accessClaims['sub'] === '23'
        && $tokens['profile']['profile_id'] === null
        && $tokens['profile']['first_name'] === 'Standalone Admin'
        && $tokens['profile']['display_name'] === 'Standalone Admin',
    'An admin without a linked profile must authenticate using its login id and stored display name.'
);

$routes = file_get_contents($root . '/api/routes.php');
$assert(
    is_string($routes)
        && str_contains($routes, "'/api/v1/auth/admin/login', 'auth/admin-login.php'")
        && str_contains($routes, "'/api/v1/admins/login-accounts', 'admins/store.php'"),
    'Separate admin login and admin account provisioning routes must be registered.'
);

echo "Phase 8 auth account-type checks passed ({$checks} assertions).\n";
