<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

$group = findOne($database, 'parent_groups', ['id' => $id]);
if ($group === null) {
        errorResponse('NOT_FOUND', 'Group not found.', 404);
    }

if (!canAccessGroup($database, $jwt, $group, 'view')) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to view this group.', 403);
    }

successResponse($group);

