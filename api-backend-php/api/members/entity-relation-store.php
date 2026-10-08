<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$memberId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (getProfile($database, $memberId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
requirePermission(canAccessMember($database, $jwt, $memberId, 'manage_relations'));
$body = getJsonBody();
$errors = [];
if (empty($body['entity_id'])) {
    $errors['entity_id'] = ['The entity_id field is required.'];
} elseif (!is_numeric($body['entity_id'])) {
    $errors['entity_id'] = ['The entity_id must be a valid integer.'];
}
$types = ['studies_at', 'works_at', 'member_of', 'volunteer_at'];
if (empty($body['relation_type'])) {
    $errors['relation_type'] = ['The relation_type field is required.'];
} elseif (!in_array($body['relation_type'], $types, true)) {
    $errors['relation_type'] = ['The relation_type must be one of: studies_at, works_at, member_of, volunteer_at.'];
}
foreach (['start_date', 'end_date'] as $field) {
    if (!empty($body[$field]) && !isValidDate((string) $body[$field])) {
        $errors[$field] = ["The {$field} must be a valid date (YYYY-MM-DD)."];
    }
}
if (!empty($body['relation_context']) && !is_array($body['relation_context'])) {
    $errors['relation_context'] = ['The relation_context must be a valid JSON object.'];
}
validateOrFail($errors);
$entityId = (int) $body['entity_id'];
if (getEntity($database, $entityId) === null) {
    errorResponse('NOT_FOUND', 'Entity not found.', 404);
}
$relationId = databaseTransaction($database, static function () use ($database, $body, $memberId, $entityId, $jwt): int {
    $database->insert('entity_member_relations', [
        'member_id' => $memberId,
        'entity_id' => $entityId,
        'relation_type' => $body['relation_type'],
        'start_date' => $body['start_date'] ?? null,
        'end_date' => $body['end_date'] ?? null,
        'is_current' => array_key_exists('is_current', $body) ? (int) (bool) $body['is_current'] : 1,
        'relation_context' => !empty($body['relation_context']) ? json_encode($body['relation_context'], JSON_THROW_ON_ERROR) : null,
    ]);
    $id = (int) $database->id();
    $row = findOne($database, 'entity_member_relations', ['id' => $id]);
    $row['entity'] = getEntity($database, $entityId);
    logActivity($database, $jwt, 'add_entity_relation', 'member_profiles', $memberId, null, $row);
    return $id;
});
$relation = findOne($database, 'entity_member_relations', ['id' => $relationId]);
$relation['is_current'] = (bool) $relation['is_current'];
if ($relation['relation_context'] !== null) {
    $relation['relation_context'] = json_decode((string) $relation['relation_context'], true);
}
$relation['entity'] = getEntity($database, $entityId);
if ($relation['start_date'] !== null) {
    $relation['start_date'] .= ' 00:00:00';
}
if ($relation['end_date'] !== null) {
    $relation['end_date'] .= ' 00:00:00';
}
successResponse($relation, 'Entity relation added successfully.', 201);

