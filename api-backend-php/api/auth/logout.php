<?php

declare(strict_types=1);

$database = $bootstrap['database'];

try {
    $user = checkAuth();
} catch (RuntimeException $exception) {
    errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
}

$body = getJsonBody();
$refreshToken = trim((string) ($body['refresh_token'] ?? ''));

if ($refreshToken === '') {
    errorResponse('VALIDATION_FAILED', 'The given data was invalid.', 422, [
        'refresh_token' => ['The refresh_token field is required.'],
    ]);
}

try {
    $refreshClaims = checkAuth('Bearer ' . $refreshToken, true);
} catch (RuntimeException $exception) {
    errorResponse('UNAUTHENTICATED', $exception->getMessage(), 401);
}

if ((int) ($refreshClaims['profile_id'] ?? 0) !== (int) ($user['profile_id'] ?? 0)) {
    errorResponse('UNAUTHENTICATED', 'Refresh token does not match the current user.', 401);
}

$database->update('refresh_tokens', [
    'revoked_at' => date('Y-m-d H:i:s'),
], [
    'profile_id' => (int) $user['profile_id'],
    'token_hash' => hash('sha256', $refreshToken),
    'revoked_at' => null,
]);

successResponse(null, 'Logged out successfully');
