<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$profileId = getUserId($jwt);
$existing = getGroupMember($database, $id, $profileId);
if ($existing === null || $existing['status'] !== 'active') {
    errorResponse('NOT_FOUND', 'You are not an active member of this group.', 404);
}
communityTransaction($database, static function () use ($database, $jwt, $id, $profileId, $existing): void {
    $database->update('group_members', ['status' => 'left'], ['id' => $existing['id'], 'status' => 'active']);
    logActivity($database, $jwt, 'group_left', 'parent_groups', $id, ['status' => 'active'], ['status' => 'left', 'member_id' => $profileId]);
});
successResponse(null, 'Left group successfully.');

