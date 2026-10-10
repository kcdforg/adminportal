<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

$roles = getUserRoles($jwt);
$canCreateLogin = count(array_intersect($roles, [
    'admin_super',
    'admin_program_manager',
    'admin_accounts',
])) > 0;
requirePermission($canCreateLogin);

$profileId = (int) ($routeParams['id'] ?? 0);
if ($profileId < 1) {
    errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [
        'id' => ['The member profile id must be a positive integer.'],
    ]);
}

$body = getJsonBody();
$usernameInput = $body['username'] ?? null;
$passwordInput = $body['password'] ?? null;
$username = is_string($usernameInput) ? trim($usernameInput) : '';
$password = is_string($passwordInput) ? $passwordInput : '';
$errors = [];

if (!is_string($usernameInput)) {
    $errors['username'] = ['The username must be a string.'];
} elseif ($username === '') {
    $errors['username'] = ['The username field is required.'];
} elseif (strlen($username) > 100 || preg_match('/[\x00-\x1F\x7F]/', $username)) {
    $errors['username'] = ['The username may not exceed 100 characters or contain control characters.'];
}
if (!is_string($passwordInput)) {
    $errors['password'] = ['The password must be a string.'];
} elseif (strlen($password) < 12) {
    $errors['password'] = ['The password must be at least 12 characters.'];
} elseif (str_contains($password, "\0")) {
    $errors['password'] = ['The password must not contain a null byte.'];
}
validateOrFail($errors);

$login = databaseTransaction($database, static function () use ($database, $profileId, $username, $password, $jwt): array {
    if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
        throw new RuntimeException('Member profile not found.', 404);
    }
    if (recordExists($database, 'user_logins', ['profile_id' => $profileId])) {
        throw new RuntimeException('This member profile already has a login.', 409);
    }
    if (recordExists($database, 'user_logins', ['username' => $username])) {
        throw new RuntimeException('This username is already in use.', 409);
    }

    $database->insert('user_logins', [
        'profile_id' => $profileId,
        'username' => $username,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'user_type' => 'member',
        'role' => null,
        'is_active' => 1,
    ]);
    $loginId = (int) $database->id();
    $created = findOne($database, 'user_logins', ['id' => $loginId], [
        'id',
        'profile_id',
        'username',
        'user_type',
        'role',
        'is_active',
        'created_at',
    ]);
    if ($created === null) {
        throw new RuntimeException('The member login could not be retrieved after creation.');
    }

    logActivity($database, $jwt, 'create', 'user_logins', $loginId, null, [
        'id' => $loginId,
        'profile_id' => $profileId,
        'username' => $username,
        'is_active' => 1,
    ]);

    return $created;
});

successResponse($login, 'Member login created successfully.', 201);
