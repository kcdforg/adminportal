<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(isElevatedAdmin($jwt));
$entity = getEntity($database, $id);
if ($entity === null) {
    errorResponse('NOT_FOUND', 'Entity not found.', 404);
}
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'entity', true));
$updated = databaseTransaction($database, static function () use ($database, $body, $entity, $jwt, $id): array {
    $update = [];
    foreach (['entity_type', 'name', 'city', 'state', 'country', 'meta', 'status'] as $field) {
        if (array_key_exists($field, $body)) {
            $update[$field] = $field === 'meta' && is_array($body[$field])
                ? json_encode($body[$field], JSON_THROW_ON_ERROR)
                : $body[$field];
        }
    }
    if ($update !== []) {
        $database->update('entities', $update, ['id' => $id]);
    }
    $row = getEntity($database, $id);
    logActivity($database, $jwt, 'update', 'entities', $id, $entity, $row);
    return $row;
});
successResponse($updated, 'Entity updated successfully.');

