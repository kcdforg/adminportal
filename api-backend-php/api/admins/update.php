<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$admin = getAdmin($database, $id);
if ($admin === null) {
    errorResponse('NOT_FOUND', 'Admin not found.', 404);
}
$body = getJsonBody();
$roleInput = $body['role'] ?? $body['admin_role'] ?? null;
$errors = [];
if ($roleInput !== null && !in_array($roleInput, ['super_admin', 'program_manager', 'accounts', 'readonly'], true)) {
    $errors['role'] = ['The role must be one of: super_admin, program_manager, accounts, readonly.'];
}
if (isset($body['status']) && !in_array($body['status'], ['active', 'inactive'], true)) {
    $errors['status'] = ['The status must be one of: active, inactive.'];
}
if (array_key_exists('display_name', $body)
    && (!is_string($body['display_name'])
        || trim($body['display_name']) === ''
        || strlen(trim($body['display_name'])) > 150
        || preg_match('/[\x00-\x1F\x7F]/', trim($body['display_name'])))) {
    $errors['display_name'] = ['The display_name must be a non-empty string of at most 150 characters without control characters.'];
}
validateOrFail($errors);
$updated = databaseTransaction($database, static function () use ($database, $body, $roleInput, $admin, $jwt, $id): array {
    $update = [];
    if ($roleInput !== null) {
        $update['role'] = $roleInput;
    }
    if (isset($body['status'])) {
        $update['is_active'] = $body['status'] === 'active' ? 1 : 0;
    }
    if (array_key_exists('display_name', $body)) {
        $update['display_name'] = trim($body['display_name']);
    }
    if ($update !== []) {
        $database->update('user_logins', $update, ['id' => $id, 'user_type' => 'admin']);
    }
    $row = getAdmin($database, $id);
    logActivity($database, $jwt, 'update', 'user_logins', $id, $admin, $row);
    return $row;
});
successResponse($updated, 'Admin updated successfully.');
