<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$groupId = (int) ($routeParams['id'] ?? 0);
$memberId = (int) ($routeParams['member_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$body = getJsonBody();
$role = $body['member_role'] ?? null;
if (!isValidGroupMemberRole($role)) {
    errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
        'member_role' => ['The member_role must be owner, admin, moderator, or member.'],
    ]);
}

try {
    $membership = communityTransaction($database, static function () use ($database, $jwt, $groupId, $memberId, $role): array {
        $group = lockCommunityGroup($database, $groupId);
        if ($group === null) {
            failCommunity('NOT_FOUND', 'Group not found.', 404);
        }
        if (!canChangeGroupRoles($database, $jwt, $group)) {
            failCommunity('UNAUTHORIZED', 'You do not have permission to change group roles.', 403);
        }
        $existing = getGroupMember($database, $groupId, $memberId);
        if ($existing === null || $existing['status'] !== 'active') {
            failCommunity('NOT_FOUND', 'An active member of this group was not found.', 404);
        }

        $targetRole = (string) ($existing['member_role'] ?? 'member');
        if (!canChangeGroupMemberRole($database, $jwt, $group, $targetRole, $role)) {
            failCommunity('UNAUTHORIZED', 'Your group role cannot make this role change.', 403);
        }

        if ($targetRole === 'owner' && $role !== 'owner') {
            assertGroupOwnerRemains($database, $groupId, $existing, (string) $group['status']);
        }
        if ($targetRole !== $role) {
            $database->update('group_members', ['member_role' => $role], ['id' => $existing['id']]);
            logActivity($database, $jwt, 'group_member_role_changed', 'parent_groups', $groupId, [
                'member_id' => $memberId,
                'member_role' => $targetRole,
            ], [
                'member_id' => $memberId,
                'member_role' => $role,
            ]);
        }
        $existing['member_role'] = $role;
        return castIds($existing);
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
}

successResponse($membership, 'Group member role updated successfully.');
