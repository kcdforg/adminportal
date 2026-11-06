<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$groupId = (int) ($routeParams['id'] ?? 0);
$memberId = (int) ($routeParams['member_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$body = getJsonBody();
validateOrFail(validateGroup($body, 'member-action'));
$action = $body['action'];
$newStatus = $action === 'ban' ? 'banned' : 'left';
$logAction = $action === 'ban' ? 'group_member_banned' : 'group_member_removed';
try {
    communityTransaction($database, static function () use ($database, $jwt, $groupId, $memberId, $newStatus, $logAction, $action): void {
        $group = lockCommunityGroup($database, $groupId);
        if ($group === null) {
            failCommunity('NOT_FOUND', 'Group not found.', 404);
        }
        $existing = getGroupMember($database, $groupId, $memberId);
        if ($existing === null) {
            failCommunity('NOT_FOUND', 'Member not found in this group.', 404);
        }
        $targetRole = (string) ($existing['member_role'] ?? 'member');
        if (!canRemoveGroupMember($database, $jwt, $group, $targetRole, $action)) {
            failCommunity('UNAUTHORIZED', 'Your group role cannot perform this member action.', 403);
        }
        assertGroupOwnerRemains($database, $groupId, $existing, (string) $group['status']);
        $oldStatus = $existing['status'];
        $database->update('group_members', ['status' => $newStatus], ['id' => $existing['id']]);
        logActivity($database, $jwt, $logAction, 'parent_groups', $groupId, [
            'status' => $oldStatus, 'member_role' => $targetRole,
        ], ['status' => $newStatus, 'member_id' => $memberId]);
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
}
successResponse(null, 'Member action applied successfully.');
