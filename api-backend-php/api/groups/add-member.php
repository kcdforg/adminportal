<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$groupId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$body = getJsonBody();
$errors = [];
$memberInput = $body['member_id'] ?? null;
if (!isPositiveIntegerInput($memberInput)) {
    $errors['member_id'] = ['The member_id must be a positive integer.'];
}
$role = $body['member_role'] ?? 'member';
if (!isValidGroupMemberRole($role)) {
    $errors['member_role'] = ['The member_role must be owner, admin, moderator, or member.'];
}
validateOrFail($errors);
$memberId = (int) $body['member_id'];

try {
    $membership = communityTransaction($database, static function () use ($database, $jwt, $groupId, $memberId, $role): array {
        $group = lockCommunityGroup($database, $groupId);
        if ($group === null) {
            failCommunity('NOT_FOUND', 'Group not found.', 404);
        }
        if ($group['status'] !== 'active') {
            failCommunity('GROUP_INACTIVE', 'Members cannot be added to an inactive or archived group.', 409);
        }
        if (!canAddGroupMember($database, $jwt, $group, $role)) {
            failCommunity('UNAUTHORIZED', 'You do not have permission to add a member with this role.', 403);
        }
        if (!isActiveGroupMemberProfile($database, $memberId)) {
            failCommunity('NOT_FOUND', 'An active member account was not found.', 404);
        }

        $existing = getGroupMember($database, $groupId, $memberId);
        if ($existing !== null) {
            if ($existing['status'] === 'active') {
                failCommunity('ALREADY_MEMBER', 'This profile is already an active member of the group.', 409);
            }
            if ($existing['status'] === 'banned') {
                failCommunity('MEMBER_BANNED', 'A banned member cannot be re-added.', 403);
            }
            $database->update('group_members', [
                'status' => 'active',
                'member_role' => $role,
                'joined_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
            $membershipId = (int) $existing['id'];
            $newRole = $role;
        } else {
            $database->insert('group_members', [
                'group_id' => $groupId,
                'member_id' => $memberId,
                'joined_at' => date('Y-m-d H:i:s'),
                'status' => 'active',
                'member_role' => $role,
            ]);
            $membershipId = (int) $database->id();
            $newRole = $role;
        }
        logActivity($database, $jwt, 'group_member_added', 'parent_groups', $groupId, null, [
            'member_id' => $memberId,
            'member_role' => $newRole,
        ]);
        return ['id' => $membershipId];
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
}

successResponse(findOne($database, 'group_members', ['id' => $membership['id']]), 'Group member added successfully.', 201);
