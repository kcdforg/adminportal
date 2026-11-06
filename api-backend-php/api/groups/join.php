<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isParent($jwt)) {
    errorResponse('UNAUTHORIZED', 'Only parents can join groups.', 403);
}
$profileId = getUserId($jwt);
$actorId = getActorId($jwt);
try {
    $memberId = communityTransaction($database, static function () use ($database, $id, $profileId, $actorId): int {
        $pdo = $database->pdo;
        $group = lockCommunityGroup($database, $id);
        if ($group === null) {
            failCommunity('NOT_FOUND', 'Group not found.', 404);
        }
        if ($group['status'] !== 'active') {
            failCommunity('GROUP_INACTIVE', 'Cannot join an inactive or archived group.');
        }
        if ($group['visibility'] !== 'public') {
            failCommunity('UNAUTHORIZED', 'Only public groups can be joined directly.', 403);
        }
        $existing = getGroupMember($database, $id, $profileId);
        if ($existing !== null) {
            if ($existing['status'] === 'banned') {
                failCommunity('UNAUTHORIZED', 'You are banned from this group.', 403);
            }
            if ($existing['status'] === 'active') {
                failCommunity('ALREADY_MEMBER', 'You are already a member of this group.', 409);
            }
            $database->update('group_members', [
                'status' => 'active',
                'member_role' => 'member',
                'joined_at' => date('Y-m-d H:i:s'),
            ], ['id' => $existing['id']]);
            logActivity($database, ['profile_id' => $actorId], 'group_joined', 'parent_groups', $id, ['status' => 'left'], ['status' => 'active', 'member_id' => $profileId]);
            return (int) $existing['id'];
        }
        $database->insert('group_members', [
            'group_id' => $id,
            'member_id' => $profileId,
            'joined_at' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'member_role' => 'member',
        ]);
        $newId = (int) $database->id();
        logActivity($database, ['profile_id' => $actorId], 'group_joined', 'parent_groups', $id, null, ['member_id' => $profileId]);
        return $newId;
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('ALREADY_MEMBER', 'You are already a member of this group.', 409);
    }
    throw $exception;
}
successResponse(findOne($database, 'group_members', ['id' => $memberId]), 'Joined group successfully.', 201);
