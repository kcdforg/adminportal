<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$roles = getUserRoles($jwt);
$canManageLogin = count(array_intersect($roles, [
    'admin_super',
    'admin_program_manager',
    'admin_accounts',
])) > 0;
requirePermission($canManageLogin);

$profileId = (int) ($routeParams['id'] ?? 0);
$result = databaseTransaction($database, static function () use ($database, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
        throw new RuntimeException('Member profile not found.', 404);
    }
    $login = findOne($database, 'user_logins', ['profile_id' => $profileId, 'user_type' => 'member']);
    if ($login === null) {
        throw new RuntimeException('Member login not found.', 404);
    }

    $database->update('user_logins', ['is_active' => 0], ['id' => $login['id']]);
    $database->update('refresh_tokens', [
        'revoked_at' => date('Y-m-d H:i:s'),
    ], [
        'login_id' => $login['id'],
        'revoked_at' => null,
    ]);
    logActivity($database, $jwt, 'deactivate', 'user_logins', (int) $login['id'], [
        'is_active' => $login['is_active'],
    ], ['is_active' => 0]);
    return ['id' => (int) $login['id'], 'profile_id' => $profileId, 'is_active' => 0];
});

successResponse($result, 'Member login deactivated successfully.');
