<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);
$trainer = getTrainerRecord($database, $id);
if ($trainer === null) {
    errorResponse('NOT_FOUND', 'Trainer not found.', 404);
}
$ownTrainer = getUserId($jwt) === (int) $trainer['profile_id']
&& in_array('trainer', getUserRoles($jwt), true);
$elevated = isElevatedAdmin($jwt);
requirePermission($elevated || $ownTrainer);
$body = getJsonBody();
$profileData = $body['profile'] ?? null;
if ($profileData !== null && !is_array($profileData)) {
    errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
        'profile' => ['The profile must be an object.'],
    ]);
}
if (!$elevated) {
    $body = array_intersect_key($body, array_flip(['bio', 'specialization']));
    $profileData = null;
}
$errors = validateIdentityFields($body, 'trainer', true);
if ($profileData !== null) {
    $profileFields = array_intersect_key($profileData, array_flip([
        'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group',
    ]));
    $errors = array_merge($errors, validateIdentityFields($profileFields, 'member', true));
    if (!empty($profileFields['email']) && $database->has('member_profiles', [
        'email' => $profileFields['email'],
        'id[!]' => (int) $trainer['profile_id'],
    ])) {
        $errors['profile.email'] = ['A profile with this email already exists.'];
    }
}
validateOrFail($errors);
$updated = databaseTransaction($database, static function () use ($database, $body, $profileData, $trainer, $jwt, $id): array {
    if ($profileData !== null) {
        $profileFields = array_intersect_key($profileData, array_flip([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group',
        ]));
        if ($profileFields !== []) {
            $database->update('member_profiles', $profileFields, ['id' => (int) $trainer['profile_id']]);
        }
    }
    $addressId = $trainer['address_id'];
    if (!empty($body['address'])) {
        if ($addressId) {
            updateAddress($database, (int) $addressId, $body['address']);
        } else {
            $addressId = createAddress($database, $body['address']);
            $database->update('trainers', ['address_id' => $addressId], ['id' => $id]);
        }
    }
    $update = [];
    foreach (['specialization', 'experience_years', 'bio', 'joined_at', 'status'] as $field) {
        if (array_key_exists($field, $body)) {
            $update[$field] = $body[$field];
        }
    }
    if ($update !== []) {
        $database->update('trainers', $update, ['id' => $id]);
    }
    $row = getTrainerRecord($database, $id);
    logActivity($database, $jwt, 'update', 'trainers', $id, $trainer, $row);
    return $row;
});
successResponse($updated, 'Trainer updated successfully.');
