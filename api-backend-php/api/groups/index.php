<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

    $where = [];
    if (!isCommunityAdmin($jwt)) {
        $profileId = getUserId($jwt);
        $memberGroupIds = $profileId > 0
            ? ($database->select('group_members', 'group_id', ['member_id' => $profileId, 'status' => 'active']) ?? [])
            : [];
        $publicIds = $database->select('parent_groups', 'id', ['visibility' => 'public']) ?? [];
        $visibleIds = array_values(array_unique(array_map('intval', array_merge($publicIds, $memberGroupIds))));
        if ($visibleIds === []) {
            $perPage = min(max((int) ($_GET['per_page'] ?? 20), 1), 100);
            jsonResponse(['success' => true, 'data' => [], 'meta' => [
                'total' => 0, 'per_page' => $perPage, 'current_page' => 1, 'last_page' => 1,
            ]]);
        }
        $where['id'] = $visibleIds;
    }

    $result = paginateCommunity($database, 'parent_groups', $_GET, $where, [
        'visibility' => 'visibility',
        'status' => 'status',
    ]);

    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

