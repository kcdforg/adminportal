<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['category_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requireGroupAdmin($jwt, 'Only platform admins can change group category status.');
$category = findOne($database, 'group_categories', ['id' => $id]);
if ($category === null) {
    errorResponse('NOT_FOUND', 'Group category not found.', 404);
}
$body = getJsonBody();
validateOrFail(validateGroupCategory($body, 'status'));
if (!isset($body['status'])) {
    errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
        'status' => ['The status field is required.'],
    ]);
}

communityTransaction($database, static function () use ($database, $jwt, $id, $category, $body): void {
    $database->update('group_categories', ['status' => $body['status']], ['id' => $id]);
    logActivity($database, $jwt, 'updated', 'group_categories', $id, [
        'status' => $category['status'],
    ], ['status' => $body['status']]);
});
$updated = findOne($database, 'group_categories', ['id' => $id]);
if ($updated === null) {
    throw new RuntimeException('The updated group category could not be retrieved.');
}
successResponse($updated, 'Group category status updated successfully.');
