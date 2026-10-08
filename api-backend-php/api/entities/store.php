<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'entity'));
$entity = databaseTransaction($database, static function () use ($database, $body, $jwt): array {
    $database->insert('entities', [
        'entity_type' => $body['entity_type'],
        'name' => $body['name'],
        'city' => $body['city'] ?? null,
        'state' => $body['state'] ?? null,
        'country' => $body['country'] ?? 'India',
        'meta' => !empty($body['meta']) ? json_encode($body['meta'], JSON_THROW_ON_ERROR) : null,
        'status' => 'active',
    ]);
    $id = (int) $database->id();
    $entity = getEntity($database, $id);
    logActivity($database, $jwt, 'create', 'entities', $id, null, $entity);
    return $entity;
});
successResponse($entity, 'Entity created successfully.', 201);

