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
$errors = [];
if (!empty($body['admin_role']) && !in_array($body['admin_role'], ['super_admin', 'program_manager', 'accounts', 'readonly'], true)) {
    $errors['admin_role'] = ['The admin_role must be one of: super_admin, program_manager, accounts, readonly.'];
}
if (!empty($body['status']) && !in_array($body['status'], ['active', 'inactive'], true)) {
    $errors['status'] = ['The status must be one of: active, inactive.'];
}
validateOrFail($errors);
$updated = databaseTransaction($database, static function () use ($database, $body, $admin, $jwt, $id): array {
    $update = [];
    foreach (['admin_role', 'status'] as $field) {
        if (array_key_exists($field, $body)) {
            $update[$field] = $body[$field];
        }
    }
    if ($update !== []) {
        $database->update('admins', $update, ['id' => $id]);
    }
    $row = getAdmin($database, $id);
    logActivity($database, $jwt, 'update', 'admins', $id, $admin, $row);
    return $row;
});
successResponse($updated, 'Admin updated successfully.');

