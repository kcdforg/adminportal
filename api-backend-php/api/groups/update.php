<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$old = findOne($database, 'parent_groups', ['id' => $id]);
if ($old === null) {
    errorResponse('NOT_FOUND', 'Group not found.', 404);
}
requireGroupPermission($database, $jwt, $old, 'settings', 'You do not have permission to update this group.');
$body = getJsonBody();
validateOrFail(validateGroup($body, 'update'));
$updates = array_intersect_key($body, array_flip(['group_name', 'display_name', 'description', 'visibility', 'status', 'category_id']));
if (isset($updates['group_name'])) {
    $updates['group_name'] = trim($updates['group_name']);
}
if (array_key_exists('display_name', $updates)) {
    $updates['display_name'] = is_string($updates['display_name']) && trim($updates['display_name']) !== ''
        ? trim($updates['display_name'])
        : null;
}
if (array_key_exists('category_id', $updates) && $updates['category_id'] !== null) {
    $category = findOne($database, 'group_categories', ['id' => (int) $updates['category_id']]);
    if ($category === null || $category['status'] !== 'active') {
        errorResponse('VALIDATION_FAILED', 'The selected category does not exist or is inactive.', 422, [
            'category_id' => ['Choose an active group category.'],
        ]);
    }
    $updates['category_id'] = (int) $updates['category_id'];
}
$id = communityTransaction($database, static function () use ($database, $jwt, $id, $updates): int {
    $current = lockCommunityGroup($database, $id);
    if ($current === null) {
        failCommunity('NOT_FOUND', 'Group not found.', 404);
    }
    if (!canManageGroupSettings($database, $jwt, $current)) {
        failCommunity('UNAUTHORIZED', 'You do not have permission to update this group.', 403);
    }
    if (array_key_exists('category_id', $updates) && $updates['category_id'] !== null) {
        $category = findOne($database, 'group_categories', ['id' => (int) $updates['category_id']]);
        if ($category === null || $category['status'] !== 'active') {
            failCommunity('INVALID_CATEGORY', 'The selected category does not exist or is inactive.', 422, [
                'category_id' => ['Choose an active group category.'],
            ]);
        }
    }
    if ($updates !== []) {
        $database->update('parent_groups', $updates, ['id' => $id]);
    }
    logActivity($database, $jwt, 'updated', 'parent_groups', $id, [
        'group_name' => $current['group_name'],
        'display_name' => $current['display_name'] ?? null,
        'visibility' => $current['visibility'],
        'status' => $current['status'],
        'category_id' => $current['category_id'] ?? null,
    ], $updates);
    return $id;
});
$updated = findOne($database, 'parent_groups', ['id' => $id]);
if ($updated === null) {
    throw new RuntimeException('The updated group could not be retrieved.');
}
successResponse(decorateGroupForUser($database, $jwt, $updated), 'Group updated successfully.');
