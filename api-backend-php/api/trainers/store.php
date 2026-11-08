<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
$profileData = $body['profile'] ?? null;
if ($profileData !== null && !is_array($profileData)) {
    errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
        'profile' => ['The profile must be an object.'],
    ]);
}
$errors = validateIdentityFields($body, 'trainer');
if ($profileData !== null) {
    $errors = array_merge($errors, validateIdentityFields($profileData, 'member'));
    if (!empty($profileData['email']) && $database->has('member_profiles', ['email' => $profileData['email']])) {
        $errors['profile.email'] = ['A profile with this email already exists.'];
    }
} else {
    $profileId = (int) ($body['profile_id'] ?? 0);
    if ($profileId < 1 || getProfile($database, $profileId) === null) {
        errorResponse('NOT_FOUND', 'Member profile not found.', 404);
    }
    if (recordExists($database, 'trainers', ['profile_id' => $profileId])) {
        errorResponse('DUPLICATE_TRAINER', 'This profile is already registered as a trainer.', 409);
    }
}
validateOrFail($errors);

$trainer = databaseTransaction($database, static function () use ($database, $body, $profileData, $jwt): array {
    if ($profileData !== null) {
        $profile = array_intersect_key($profileData, array_flip([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group',
        ]));
        $database->insert('member_profiles', $profile);
        $profileId = (int) $database->id();
    } else {
        $profileId = (int) $body['profile_id'];
        if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
            errorResponse('NOT_FOUND', 'Member profile not found.', 404);
        }
        if (recordExists($database, 'trainers', ['profile_id' => $profileId])) {
            errorResponse('DUPLICATE_TRAINER', 'This profile is already registered as a trainer.', 409);
        }
    }

    $addressId = !empty($body['address']) ? createAddress($database, $body['address']) : null;
    $database->insert('trainers', [
        'profile_id' => $profileId,
        'trainer_code' => 'TR-TEMP-' . bin2hex(random_bytes(8)),
        'specialization' => $body['specialization'] ?? null,
        'experience_years' => $body['experience_years'] ?? null,
        'bio' => $body['bio'] ?? null,
        'joined_at' => $body['joined_at'] ?? null,
        'address_id' => $addressId,
        'status' => 'active',
    ]);
    $id = (int) $database->id();
    $database->update('trainers', [
        'trainer_code' => 'TR-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT),
    ], ['id' => $id]);
    $trainer = getTrainerRecord($database, $id);
    logActivity($database, $jwt, 'create', 'trainers', $id, null, $trainer);
    return $trainer;
});
successResponse($trainer, 'Trainer created successfully.', 201);
