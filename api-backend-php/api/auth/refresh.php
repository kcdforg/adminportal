<?php

declare(strict_types=1);

$database = $bootstrap['database'];
$body = getJsonBody();
$refreshToken = trim((string) ($body['refresh_token'] ?? ''));

if ($refreshToken === '') {
    errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [
        'refresh_token' => ['The refresh_token field is required.'],
    ]);
}

try {
    $claims = checkAuth('Bearer ' . $refreshToken, true);
} catch (RuntimeException $exception) {
    errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
}

if (($claims['type'] ?? 'access') !== 'refresh') {
    errorResponse('UNAUTHENTICATED', 'Invalid or expired refresh token.', 401);
}

$profileId = (int) ($claims['profile_id'] ?? 0);
$tokenHash = hash('sha256', $refreshToken);

$storedToken = $database->get('refresh_tokens', ['id', 'profile_id', 'expires_at'], [
    'token_hash' => $tokenHash,
    'profile_id' => $profileId,
    'revoked_at' => null,
    'expires_at[>]' => date('Y-m-d H:i:s'),
]);

if (!$storedToken) {
    errorResponse('UNAUTHENTICATED', 'Refresh token has been revoked or expired.', 401);
}

$pdo = $database->pdo;
$pdo->beginTransaction();

try {
    $database->update('refresh_tokens', [
        'revoked_at' => date('Y-m-d H:i:s'),
    ], [
        'id' => (int) $storedToken['id'],
        'revoked_at' => null,
    ]);

    $profile = getProfileById($database, $profileId);
    if ($profile === null) {
        throw new RuntimeException('Profile not found.');
    }

    $roleData = getProfileRoles($database, $profileId);

    $accessToken = createToken([
        'profile_id' => $profileId,
        'username' => (string) ($claims['username'] ?? $profile['email'] ?? 'user'),
        'roles' => $roleData['roles'],
        'family_ids' => $roleData['family_ids'],
    ], 'access');

    $newRefreshToken = createToken(['profile_id' => $profileId], 'refresh');

    $database->insert('refresh_tokens', [
        'profile_id' => $profileId,
        'token_hash' => hash('sha256', $newRefreshToken),
        'expires_at' => date('Y-m-d H:i:s', time() + (int) ($bootstrap['config']['jwt']['refresh_ttl'] ?? 2592000)),
        'created_at' => date('Y-m-d H:i:s'),
    ]);

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    errorResponse('UNAUTHENTICATED', 'Invalid or expired refresh token.', 401);
}

successResponse([
    'access_token' => $accessToken,
    'refresh_token' => $newRefreshToken,
    'token_type' => 'Bearer',
    'expires_in' => (int) ($bootstrap['config']['jwt']['access_ttl'] ?? 900),
    'profile' => [
        'id' => (int) $profile['id'],
        'first_name' => $profile['first_name'],
        'last_name' => $profile['last_name'],
        'roles' => $roleData['roles'],
        'family_ids' => $roleData['family_ids'],
    ],
], 'Token refreshed');
