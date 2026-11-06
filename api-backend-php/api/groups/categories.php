<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isCommunityAdmin($jwt) && ($jwt['user_type'] ?? null) !== 'member') {
    errorResponse('UNAUTHORIZED', 'Only authenticated admins and members can view group categories.', 403);
}

$where = isCommunityAdmin($jwt) ? [] : ['status' => 'active'];
$categories = $database->select('group_categories', '*', array_merge($where, [
    'ORDER' => ['category_name' => 'ASC'],
])) ?? [];
successResponse(array_map('castIds', $categories));
