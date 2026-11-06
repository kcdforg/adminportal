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

    if (isset($_GET['category_id']) && (!ctype_digit((string) $_GET['category_id']) || (int) $_GET['category_id'] < 1)) {
        errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
            'category_id' => ['The category_id must be a positive integer.'],
        ]);
    }

    $result = paginateCommunity($database, 'parent_groups', $_GET, $where, [
        'visibility' => 'visibility',
        'status' => 'status',
        'category_id' => 'category_id',
    ]);

    $result['data'] = array_map(
        static fn (array $group): array => decorateGroupForUser($database, $jwt, $group),
        $result['data']
    );
    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);
