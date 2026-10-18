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
$body = getJsonBody();
$errors = [];
$update = [];
$allowedFields = ['username', 'display_name', 'password', 'is_active'];
foreach (array_diff(array_keys($body), $allowedFields) as $field) {
    $errors[$field] = ['This field is not supported for member login updates.'];
}

if (array_key_exists('username', $body)) {
    if (!is_string($body['username'])) {
        $errors['username'] = ['The username must be a string.'];
    } else {
        $username = trim($body['username']);
        if ($username === '' || strlen($username) > 100 || preg_match('/[\x00-\x1F\x7F]/', $username)) {
            $errors['username'] = ['The username is required, must be at most 100 characters, and must not contain control characters.'];
        } else {
            $update['username'] = $username;
        }
    }
}
if (array_key_exists('display_name', $body)) {
    if (!is_string($body['display_name'])) {
        $errors['display_name'] = ['The display_name must be a string.'];
    } else {
        $displayName = trim($body['display_name']);
        if ($displayName === '' || strlen($displayName) > 150 || preg_match('/[\x00-\x1F\x7F]/', $displayName)) {
            $errors['display_name'] = ['The display_name is required, must be at most 150 characters, and must not contain control characters.'];
        } else {
            $update['display_name'] = $displayName;
        }
    }
}
if (array_key_exists('password', $body)) {
    if (!is_string($body['password']) || $body['password'] === '' || str_contains($body['password'], "\0")) {
        $errors['password'] = ['The password must be a non-empty string without a null byte.'];
    } else {
        $update['password_hash'] = password_hash($body['password'], PASSWORD_DEFAULT);
    }
}
if (array_key_exists('is_active', $body)) {
    if (!is_bool($body['is_active'])) {
        $errors['is_active'] = ['The is_active field must be a boolean.'];
    } else {
        $update['is_active'] = $body['is_active'] ? 1 : 0;
    }
}
if ($update === []) {
    $errors['login'] = ['At least one login field must be provided.'];
}
validateOrFail($errors);

$result = databaseTransaction($database, static function () use ($database, $profileId, $update, $jwt): array {
    if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
        throw new RuntimeException('Member profile not found.', 404);
    }
    $old = findOne($database, 'user_logins', ['profile_id' => $profileId, 'user_type' => 'member']);
    if ($old === null) {
        throw new RuntimeException('Member login not found.', 404);
    }
    if (isset($update['username']) && recordExists($database, 'user_logins', [
        'username' => $update['username'],
        'id[!]' => $old['id'],
    ])) {
        throw new RuntimeException('This username is already in use.', 409);
    }

    $database->update('user_logins', $update, ['id' => $old['id']]);
    if (($update['is_active'] ?? 1) === 0 || isset($update['password_hash'])) {
        $database->update('refresh_tokens', [
            'revoked_at' => date('Y-m-d H:i:s'),
        ], [
            'login_id' => $old['id'],
            'revoked_at' => null,
        ]);
    }

    $login = findOne($database, 'user_logins', ['id' => $old['id']], [
        'id', 'profile_id', 'username', 'display_name', 'user_type', 'role',
        'is_active', 'last_login_at', 'created_at', 'updated_at',
    ]);
    logActivity($database, $jwt, 'update', 'user_logins', (int) $old['id'], [
        'username' => $old['username'],
        'display_name' => $old['display_name'],
        'is_active' => $old['is_active'],
    ], [
        'username' => $login['username'],
        'display_name' => $login['display_name'],
        'is_active' => $login['is_active'],
    ]);
    return $login;
});

successResponse($result, 'Member login updated successfully.');
