<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requireGroupAdmin($jwt, 'Only platform admins can create group categories.');
$body = getJsonBody();
validateOrFail(validateGroupCategory($body, 'create'));

try {
    $id = communityTransaction($database, static function () use ($database, $jwt, $body): int {
        $values = [
            'category_name' => trim($body['category_name']),
            'description' => !empty($body['description']) ? $body['description'] : null,
            'status' => 'active',
        ];
        $database->insert('group_categories', $values);
        $id = (int) $database->id();
        logActivity($database, $jwt, 'created', 'group_categories', $id, null, $values);
        return $id;
    });
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('CATEGORY_EXISTS', 'A category with this name already exists.', 409);
    }
    throw $exception;
}

$category = findOne($database, 'group_categories', ['id' => $id]);
if ($category === null) {
    throw new RuntimeException('The created group category could not be retrieved.');
}
successResponse($category, 'Group category created successfully.', 201);
