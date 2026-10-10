<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$body = getJsonBody();
$errors = [];
if (isset($body['profile_id'])
    && (filter_var($body['profile_id'], FILTER_VALIDATE_INT) === false || (int) $body['profile_id'] < 1)) {
    $errors['profile_id'] = ['The profile_id must be a positive integer.'];
}
$roles = ['super_admin', 'program_manager', 'accounts', 'readonly'];
if (empty($body['role']) && empty($body['admin_role'])) {
    $errors['role'] = ['The role field is required.'];
} elseif (!in_array($body['role'] ?? $body['admin_role'], $roles, true)) {
    $errors['role'] = ['The role must be one of: super_admin, program_manager, accounts, readonly.'];
}
if (!isset($body['username']) || !is_string($body['username'])) {
    $errors['username'] = ['The username field is required and must be a string.'];
} elseif (trim($body['username']) === '' || strlen(trim($body['username'])) > 100 || preg_match('/[\x00-\x1F\x7F]/', trim($body['username']))) {
    $errors['username'] = ['The username is invalid.'];
}
if (array_key_exists('display_name', $body)
    && (!is_string($body['display_name'])
        || trim($body['display_name']) === ''
        || strlen(trim($body['display_name'])) > 150
        || preg_match('/[\x00-\x1F\x7F]/', trim($body['display_name'])))) {
    $errors['display_name'] = ['The display_name must be a non-empty string of at most 150 characters without control characters.'];
}
if (!isset($body['password']) || !is_string($body['password'])) {
    $errors['password'] = ['The password field is required and must be a string.'];
} elseif (strlen($body['password']) < 12) {
    $errors['password'] = ['The password must be at least 12 characters.'];
} elseif (str_contains($body['password'], "\0")) {
    $errors['password'] = ['The password must not contain a null byte.'];
}
if (isset($body['profile_id']) && is_string($body['profile_id']) && trim($body['profile_id']) === '') {
    $errors['profile_id'] = ['The profile_id must be a positive integer.'];
}
validateOrFail($errors);
$profileId = isset($body['profile_id']) ? (int) $body['profile_id'] : null;
if ($profileId !== null && getProfile($database, $profileId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
$username = trim($body['username']);
$displayName = array_key_exists('display_name', $body) ? trim($body['display_name']) : null;
$role = $body['role'] ?? $body['admin_role'];
$admin = databaseTransaction($database, static function () use ($database, $profileId, $username, $displayName, $body, $role, $jwt): array {
    if ($profileId !== null) {
        if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
            throw new RuntimeException('Member profile not found.', 404);
        }
        if (recordExists($database, 'user_logins', ['profile_id' => $profileId])) {
            throw new RuntimeException('This profile already has a login account.', 409);
        }
    }
    if (recordExists($database, 'user_logins', ['username' => $username])) {
        throw new RuntimeException('This username is already in use.', 409);
    }
    $database->insert('user_logins', [
        'profile_id' => $profileId,
        'username' => $username,
        'display_name' => $displayName ?? resolveLoginDisplayName($database, $profileId, $username),
        'password_hash' => password_hash($body['password'], PASSWORD_DEFAULT),
        'user_type' => 'admin',
        'role' => $role,
        'is_active' => 1,
    ]);
    $id = (int) $database->id();
    $admin = getAdmin($database, $id);
    logActivity($database, $jwt, 'create', 'user_logins', $id, null, $admin);
    return $admin;
});
successResponse($admin, 'Admin login account created successfully.', 201);
