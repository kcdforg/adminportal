<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requireGroupAdmin($jwt, 'Only admins can create groups.');
$body = getJsonBody();
validateOrFail(validateGroup($body, 'create'));
if (!empty($body['category_id'])) {
    $category = findOne($database, 'group_categories', ['id' => (int) $body['category_id']]);
    if ($category === null || $category['status'] !== 'active') {
        errorResponse('VALIDATION_FAILED', 'The selected category does not exist or is inactive.', 422, [
            'category_id' => ['Choose an active group category.'],
        ]);
    }
}
$ownerMemberId = isset($body['owner_member_id']) ? (int) $body['owner_member_id'] : null;
if ($ownerMemberId !== null && !isActiveGroupMemberProfile($database, $ownerMemberId)) {
    errorResponse('VALIDATION_FAILED', 'The selected owner is not an active member account.', 422, [
        'owner_member_id' => ['Choose an active profile with an enabled member login as the initial group owner.'],
    ]);
}
$actorId = getActorId($jwt);

$id = communityTransaction($database, static function () use ($database, $body, $actorId, $ownerMemberId): int {
    $values = [
        'group_name' => trim($body['group_name']),
        'display_name' => isset($body['display_name']) && trim($body['display_name']) !== ''
            ? trim($body['display_name'])
            : null,
        'description' => !empty($body['description']) ? $body['description'] : null,
        'visibility' => $body['visibility'] ?? 'public',
        'status' => 'active',
    ];
    if (array_key_exists('category_id', $body)) {
        $values['category_id'] = $body['category_id'] === null ? null : (int) $body['category_id'];
    }
    $database->insert('parent_groups', $values);
    $groupId = (int) $database->id();
    if ($ownerMemberId !== null) {
        $database->insert('group_members', [
            'group_id' => $groupId,
            'member_id' => $ownerMemberId,
            'joined_at' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'member_role' => 'owner',
        ]);
    }
    logActivity($database, ['profile_id' => $actorId], 'created', 'parent_groups', $groupId, null, [
        'group_name' => $values['group_name'],
        'display_name' => $values['display_name'],
        'visibility' => $values['visibility'],
        'category_id' => $values['category_id'] ?? null,
        'owner_member_id' => $ownerMemberId,
    ]);
    return $groupId;
});

$created = findOne($database, 'parent_groups', ['id' => $id]);
if ($created === null) {
    throw new RuntimeException('The created group could not be retrieved.');
}
successResponse(decorateGroupForUser($database, $jwt, $created), 'Group created successfully.', 201);
