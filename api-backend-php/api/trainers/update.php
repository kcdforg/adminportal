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
if (!$elevated) {
    $body = array_intersect_key($body, array_flip(['bio', 'specialization']));
}
validateOrFail(validateIdentityFields($body, 'trainer', true));
$updated = databaseTransaction($database, static function () use ($database, $body, $trainer, $jwt, $id): array {
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

