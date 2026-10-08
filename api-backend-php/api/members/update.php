<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);
$profile = getProfile($database, $id);
if ($profile === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
requirePermission(canAccessMember($database, $jwt, $id, 'edit'));
$body = getJsonBody();
$errors = validateIdentityFields($body, 'member', true);
if (!empty($body['email']) && $database->has('member_profiles', [
    'email' => $body['email'],
    'id[!]' => $id,
])) {
    $errors['email'] = ['A profile with this email already exists.'];
}
validateOrFail($errors);
$allowed = ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group', 'status'];
$update = [];
foreach ($allowed as $field) {
    if (array_key_exists($field, $body)) {
        $update[$field] = $body[$field];
    }
}
$updated = databaseTransaction($database, static function () use ($database, $update, $profile, $jwt, $id): array {
    if ($update !== []) {
        $database->update('member_profiles', $update, ['id' => $id]);
    }
    $row = getProfile($database, $id);
    logActivity($database, $jwt, 'update', 'member_profiles', $id, $profile, $row);
    return $row;
});
successResponse($updated, 'Member profile updated successfully.');

