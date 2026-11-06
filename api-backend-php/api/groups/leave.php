<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$profileId = getUserId($jwt);
if (($jwt['user_type'] ?? null) !== 'member' || !isParent($jwt) || $profileId <= 0) {
    errorResponse('UNAUTHORIZED', 'Only authenticated members can leave groups.', 403);
}
$existing = getGroupMember($database, $id, $profileId);
if ($existing === null || $existing['status'] !== 'active') {
    errorResponse('NOT_FOUND', 'You are not an active member of this group.', 404);
}
try {
    communityTransaction($database, static function () use ($database, $jwt, $id, $profileId): void {
        $group = lockCommunityGroup($database, $id);
        if ($group === null) {
            failCommunity('NOT_FOUND', 'Group not found.', 404);
        }
        $membership = getGroupMember($database, $id, $profileId);
        if ($membership === null || $membership['status'] !== 'active') {
            failCommunity('NOT_FOUND', 'You are not an active member of this group.', 404);
        }
        assertGroupOwnerRemains($database, $id, $membership, (string) $group['status']);
        $database->update('group_members', ['status' => 'left'], ['id' => $membership['id'], 'status' => 'active']);
        logActivity($database, $jwt, 'group_left', 'parent_groups', $id, [
            'status' => 'active', 'member_role' => $membership['member_role'],
        ], ['status' => 'left', 'member_id' => $profileId]);
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
}
successResponse(null, 'Left group successfully.');
