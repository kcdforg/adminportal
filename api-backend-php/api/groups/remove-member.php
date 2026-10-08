<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$groupId = (int) ($routeParams['id'] ?? 0);
$memberId = (int) ($routeParams['member_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requireGroupAdmin($jwt, 'Only admins can remove or ban group members.');
$body = getJsonBody();
validateOrFail(validateGroup($body, 'member-action'));
$existing = getGroupMember($database, $groupId, $memberId);
if ($existing === null) {
    errorResponse('NOT_FOUND', 'Member not found in this group.', 404);
}
$action = $body['action'];
$oldStatus = $existing['status'];
$newStatus = $action === 'ban' ? 'banned' : 'left';
$logAction = $action === 'ban' ? 'group_member_banned' : 'group_member_removed';
communityTransaction($database, static function () use ($database, $jwt, $groupId, $memberId, $existing, $newStatus, $logAction, $oldStatus): void {
    $database->update('group_members', ['status' => $newStatus], ['id' => $existing['id']]);
    logActivity($database, $jwt, $logAction, 'parent_groups', $groupId, ['status' => $oldStatus], ['status' => $newStatus, 'member_id' => $memberId]);
});
successResponse(null, 'Member action applied successfully.');

