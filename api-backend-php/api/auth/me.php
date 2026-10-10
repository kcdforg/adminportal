<?php

declare(strict_types=1);

$database = $bootstrap['database'];

try {
    $payload = checkAuth();
} catch (RuntimeException $exception) {
    errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
}

$profileId = (int) ($payload['profile_id'] ?? 0);
if ((int) ($payload['login_id'] ?? 0) <= 0) {
    errorResponse('UNAUTHENTICATED', 'The token is missing login information.', 401);
}

$login = $database->get('user_logins', [
    'id', 'profile_id', 'username', 'display_name', 'user_type', 'role', 'is_active', 'created_at', 'updated_at', 'last_login_at',
], [
    'id' => (int) $payload['login_id'],
    'user_type' => (string) $payload['user_type'],
    'is_active' => 1,
]);
$profile = $profileId > 0 ? getProfileById($database, $profileId) : null;

if (!$login || (int) ($login['profile_id'] ?? 0) !== $profileId) {
    errorResponse('UNAUTHENTICATED', 'The login account is no longer active.', 401);
}
if ((string) $login['user_type'] === 'member' && !$profile) {
    errorResponse('PROFILE_NOT_FOUND', 'The authenticated profile could not be found.', 404);
}

$roles = getUserRoles($payload);

$displayName = resolveLoginDisplayName(
    $database,
    $profileId > 0 ? $profileId : null,
    (string) $login['username'],
    is_string($login['display_name']) ? $login['display_name'] : null
);
$firstName = (string) ($profile['first_name'] ?? $displayName);
$lastName = (string) ($profile['last_name'] ?? '');
$name = trim(implode(' ', array_filter([
    $firstName,
    (string) ($profile['middle_name'] ?? ''),
    $lastName,
])));

successResponse([
    'id' => $profileId > 0 ? $profileId : (int) $login['id'],
    'profile_id' => $profileId > 0 ? $profileId : null,
    'login_id' => (int) $login['id'],
    'username' => (string) $login['username'],
    'display_name' => $displayName,
    'email' => (string) ($profile['email'] ?? ''),
    'first_name' => $firstName,
    'last_name' => $lastName,
    'name' => $name,
    'roles' => $roles,
    'user_type' => (string) $login['user_type'],
    'role' => $login['role'],
    'admin_role' => $login['role'],
    'portal' => (string) $payload['portal'],
    'profile_type' => 'user_login',
    'is_active' => (bool) $login['is_active'],
    'last_login_at' => $login['last_login_at'],
    'created_at' => $login['created_at'],
    'updated_at' => $login['updated_at'],
], 'Profile retrieved successfully');
