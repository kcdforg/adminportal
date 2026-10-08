<?php

declare(strict_types=1);

$database = $bootstrap['database'];

try {
    $payload = checkAuth();
} catch (RuntimeException $exception) {
    errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
}

$profileId = (int) ($payload['profile_id'] ?? 0);
if ($profileId <= 0) {
    errorResponse('UNAUTHENTICATED', 'The token is missing profile information.', 401);
}

$login = $database->get('user_logins', [
    'username', 'is_active', 'created_at', 'updated_at', 'last_login_at',
], ['profile_id' => $profileId]);
$profile = getProfileById($database, $profileId);

if (!$login || !$profile) {
    errorResponse('PROFILE_NOT_FOUND', 'The authenticated profile could not be found.', 404);
}

$roles = array_values(array_unique(array_filter(
    array_map('strval', (array) ($payload['roles'] ?? []))
)));

$firstName = (string) $profile['first_name'];
$lastName = (string) $profile['last_name'];
$name = trim(implode(' ', array_filter([
    $firstName,
    (string) ($profile['middle_name'] ?? ''),
    $lastName,
])));

successResponse([
    'id' => $profileId,
    'username' => (string) $login['username'],
    'email' => (string) ($profile['email'] ?? ''),
    'first_name' => $firstName,
    'last_name' => $lastName,
    'name' => $name,
    'roles' => $roles,
    'profile_type' => 'user_login',
    'is_active' => (bool) $login['is_active'],
    'last_login_at' => $login['last_login_at'],
    'created_at' => $login['created_at'],
    'updated_at' => $login['updated_at'],
], 'Profile retrieved successfully');
