<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requireGroupAdmin($jwt, 'Only admins can update groups.');
$old = findOne($database, 'parent_groups', ['id' => $id]);
if ($old === null) {
    errorResponse('NOT_FOUND', 'Group not found.', 404);
}
$body = getJsonBody();
validateOrFail(validateGroup($body, 'update'));
$updates = array_intersect_key($body, array_flip(['group_name', 'description', 'visibility', 'status']));
$id = communityTransaction($database, static function () use ($database, $jwt, $id, $old, $updates): int {
    if ($updates !== []) {
        $database->update('parent_groups', $updates, ['id' => $id]);
    }
    logActivity($database, $jwt, 'updated', 'parent_groups', $id, [
        'group_name' => $old['group_name'],
        'visibility' => $old['visibility'],
        'status' => $old['status'],
    ], $updates);
    return $id;
});
successResponse(findOne($database, 'parent_groups', ['id' => $id]), 'Group updated successfully.');

