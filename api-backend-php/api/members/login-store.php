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
$displayNameInput = $body['display_name'] ?? null;
$username = is_string($usernameInput) ? trim($usernameInput) : '';
$password = is_string($passwordInput) ? $passwordInput : '';
$displayName = is_string($displayNameInput) ? trim($displayNameInput) : '';
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
} elseif ($password === '') {
    $errors['password'] = ['The password field is required.'];
} elseif (str_contains($password, "\0")) {
    $errors['password'] = ['The password must not contain a null byte.'];
}
if ($displayNameInput !== null
    && (!is_string($displayNameInput) || $displayName === '' || strlen($displayName) > 150 || preg_match('/[\x00-\x1F\x7F]/', $displayName))) {
    $errors['display_name'] = ['The display_name must be a non-empty string of at most 150 characters without control characters.'];
}
validateOrFail($errors);

$login = databaseTransaction($database, static function () use ($database, $profileId, $username, $password, $displayName, $displayNameInput, $jwt): array {
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
        'display_name' => resolveLoginDisplayName(
            $database,
            $profileId,
            $username,
            $displayNameInput === null ? null : $displayName
        ),
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
        'display_name',
        'user_type',
        'role',
        'is_active',
        'last_login_at',
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
