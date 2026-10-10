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
$loginId = (int) ($claims['login_id'] ?? 0);
$userType = (string) ($claims['user_type'] ?? '');
$portal = (string) ($claims['portal'] ?? '');
if ($loginId <= 0
    || !in_array($userType, ['admin', 'member'], true)
    || $portal !== $userType
    || ($userType === 'member' && $profileId <= 0)) {
    errorResponse('UNAUTHENTICATED', 'Invalid or expired refresh token.', 401);
}
$tokenHash = hash('sha256', $refreshToken);

$storedToken = $database->get('refresh_tokens', ['id', 'login_id', 'profile_id', 'expires_at'], [
    'token_hash' => $tokenHash,
    'login_id' => $loginId,
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

    $login = $database->get('user_logins', [
        'id',
        'profile_id',
        'username',
        'display_name',
        'user_type',
        'role',
        'is_active',
    ], [
        'id' => $loginId,
        'user_type' => $userType,
        'is_active' => 1,
    ]);
    if (!is_array($login)) {
        throw new RuntimeException('The login account is no longer active.');
    }
    $currentProfileId = (int) ($login['profile_id'] ?? 0);
    $profile = $currentProfileId > 0 ? getProfileById($database, $currentProfileId) : null;
    if ($currentProfileId !== $profileId
        || ($userType === 'member' && $profile === null)
        || ($userType === 'admin' && $currentProfileId > 0 && $profile === null)) {
        throw new RuntimeException('The login account is no longer active.');
    }

    $tokens = issueLoginTokens($database, $bootstrap, castIds($login), $profile, false);

    $pdo->commit();
} catch (Throwable $exception) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    errorResponse('UNAUTHENTICATED', 'Invalid or expired refresh token.', 401);
}

successResponse($tokens, 'Token refreshed');
