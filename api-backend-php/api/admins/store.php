<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$body = getJsonBody();
$errors = [];
if (empty($body['profile_id'])) {
    $errors['profile_id'] = ['The profile_id field is required.'];
} elseif (!is_numeric($body['profile_id'])) {
    $errors['profile_id'] = ['The profile_id must be a valid integer.'];
}
$roles = ['super_admin', 'program_manager', 'accounts', 'readonly'];
if (empty($body['admin_role'])) {
    $errors['admin_role'] = ['The admin_role field is required.'];
} elseif (!in_array($body['admin_role'], $roles, true)) {
    $errors['admin_role'] = ['The admin_role must be one of: super_admin, program_manager, accounts, readonly.'];
}
validateOrFail($errors);
$profileId = (int) $body['profile_id'];
if (getProfile($database, $profileId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
$admin = databaseTransaction($database, static function () use ($database, $body, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
        throw new RuntimeException('Member profile not found.', 404);
    }
    if (recordExists($database, 'admins', ['profile_id' => $profileId])) {
        throw new RuntimeException('This profile is already registered as an admin.', 409);
    }
    $database->insert('admins', [
        'profile_id' => $profileId,
        'admin_role' => $body['admin_role'],
        'status' => 'active',
    ]);
    $id = (int) $database->id();
    $admin = getAdmin($database, $id);
    logActivity($database, $jwt, 'create', 'admins', $id, null, $admin);
    return $admin;
});
successResponse($admin, 'Admin created successfully.', 201);

