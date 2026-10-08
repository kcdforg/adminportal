<?php

declare(strict_types=1);

$database = $bootstrap['database'];
$body = getJsonBody();
$username = trim((string) ($body['username'] ?? ''));
$password = (string) ($body['password'] ?? '');

$errors = [];
if ($username === '') {
    $errors['username'] = ['The username field is required.'];
}
if ($password === '') {
    $errors['password'] = ['The password field is required.'];
}
if ($errors !== []) {
    errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, $errors);
}

$login = $database->get('user_logins', [
    'id', 'profile_id', 'username', 'password_hash', 'is_active'
], [
    'username' => $username,
]);

if (!$login || !password_verify($password, (string) $login['password_hash'])) {
    errorResponse('UNAUTHENTICATED', 'Invalid credentials.', 401);
}

if ((int) $login['is_active'] !== 1) {
    errorResponse('UNAUTHENTICATED', 'Account is deactivated.', 401);
}

$profileId = (int) $login['profile_id'];
$profile = getProfileById($database, $profileId);
if ($profile === null) {
    errorResponse('UNAUTHENTICATED', 'The user profile could not be found.', 401);
}

$roleData = getProfileRoles($database, $profileId);

$accessToken = createToken([
    'profile_id' => $profileId,
    'username' => $username,
    'roles' => $roleData['roles'],
    'family_ids' => $roleData['family_ids'],
], 'access');

$refreshToken = createToken([
    'profile_id' => $profileId,
], 'refresh');

$database->insert('refresh_tokens', [
    'profile_id' => $profileId,
    'token_hash' => hash('sha256', $refreshToken),
    'expires_at' => date('Y-m-d H:i:s', time() + (int) ($bootstrap['config']['jwt']['refresh_ttl'] ?? 2592000)),
    'created_at' => date('Y-m-d H:i:s'),
]);

$database->update('user_logins', [
    'last_login_at' => date('Y-m-d H:i:s'),
], [
    'id' => (int) $login['id'],
]);

successResponse([
    'access_token' => $accessToken,
    'refresh_token' => $refreshToken,
    'token_type' => 'Bearer',
    'expires_in' => (int) ($bootstrap['config']['jwt']['access_ttl'] ?? 900),
    'profile' => [
        'id' => $profileId,
        'first_name' => $profile['first_name'],
        'last_name' => $profile['last_name'],
        'roles' => $roleData['roles'],
        'family_ids' => $roleData['family_ids'],
    ],
], 'Login successful');
