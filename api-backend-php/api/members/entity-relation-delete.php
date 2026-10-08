<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$memberId = (int) ($routeParams['id'] ?? 0);
$relationId = (int) ($routeParams['relation_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (getProfile($database, $memberId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
requirePermission(canAccessMember($database, $jwt, $memberId, 'manage_relations'));
$relation = findOne($database, 'entity_member_relations', [
    'id' => $relationId,
    'member_id' => $memberId,
]);
if ($relation === null) {
    errorResponse('NOT_FOUND', 'Entity relation not found.', 404);
}
databaseTransaction($database, static function () use ($database, $relation, $relationId, $memberId, $jwt): void {
    $database->update('entity_member_relations', [
        'is_current' => 0,
        'end_date' => date('Y-m-d'),
    ], ['id' => $relationId]);
    logActivity($database, $jwt, 'remove_entity_relation', 'member_profiles', $memberId, $relation, [
        'is_current' => false,
        'end_date' => date('Y-m-d'),
    ]);
});
successResponse(null, 'Entity relation removed successfully.');

