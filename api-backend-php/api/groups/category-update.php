<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['category_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requireGroupAdmin($jwt, 'Only platform admins can update group categories.');
$category = findOne($database, 'group_categories', ['id' => $id]);
if ($category === null) {
    errorResponse('NOT_FOUND', 'Group category not found.', 404);
}
$body = getJsonBody();
validateOrFail(validateGroupCategory($body, 'update'));
$updates = array_intersect_key($body, array_flip(['category_name', 'description']));
if (isset($updates['category_name'])) {
    $updates['category_name'] = trim((string) $updates['category_name']);
}
if (array_key_exists('description', $updates) && $updates['description'] === '') {
    $updates['description'] = null;
}

try {
    communityTransaction($database, static function () use ($database, $jwt, $id, $category, $updates): void {
        if ($updates !== []) {
            $database->update('group_categories', $updates, ['id' => $id]);
        }
        logActivity($database, $jwt, 'updated', 'group_categories', $id, [
            'category_name' => $category['category_name'],
            'description' => $category['description'],
        ], $updates);
    });
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('CATEGORY_EXISTS', 'A category with this name already exists.', 409);
    }
    throw $exception;
}
$updated = findOne($database, 'group_categories', ['id' => $id]);
if ($updated === null) {
    throw new RuntimeException('The updated group category could not be retrieved.');
}
successResponse($updated, 'Group category updated successfully.');
