<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$memberId = (int) ($routeParams['id'] ?? 0);
if (getProfile($database, $memberId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
requirePermission(canAccessMember($database, $jwt, $memberId, 'view'));
$relations = findMany($database, 'entity_member_relations', ['*'], [
    'member_id' => $memberId,
    'ORDER' => ['is_current' => 'DESC', 'start_date' => 'DESC'],
]);
foreach ($relations as &$relation) {
    if ($relation['start_date'] !== null) {
        $relation['start_date'] .= ' 00:00:00';
    }
    if ($relation['end_date'] !== null) {
        $relation['end_date'] .= ' 00:00:00';
    }
    $relation['is_current'] = (bool) $relation['is_current'];
    if ($relation['relation_context'] !== null) {
        $relation['relation_context'] = json_decode((string) $relation['relation_context'], true);
    }
    $relation['entity'] = getEntity($database, (int) $relation['entity_id']);
}
unset($relation);
successResponse($relations);

