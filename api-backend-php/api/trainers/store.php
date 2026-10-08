<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'trainer'));
$profileId = (int) $body['profile_id'];
if (getProfile($database, $profileId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
$trainer = databaseTransaction($database, static function () use ($database, $body, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'member_profiles', $profileId)) {
        throw new RuntimeException('Member profile not found.', 404);
    }
    if (recordExists($database, 'trainers', ['profile_id' => $profileId])) {
        throw new RuntimeException('This profile is already registered as a trainer.', 409);
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

