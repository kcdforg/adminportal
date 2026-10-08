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

if (!canAccessGroup($database, $jwt, $group, 'members')) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to view members of this group.', 403);
    }

$rows = $database->select('group_members', ['[>]member_profiles' => ['member_id' => 'id']], [
        'group_members.id',
        'group_members.group_id',
        'group_members.member_id',
        'group_members.joined_at',
        'group_members.status',
        'member_profiles.first_name',
        'member_profiles.last_name',
        'member_profiles.mobile',
        'member_profiles.email',
    ], ['group_members.group_id' => $id, 'group_members.status' => 'active']) ?? [];

$members = [];
foreach ($rows as $row) {
        $row = castIds($row);
        $row['member_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
        unset($row['first_name'], $row['last_name']);
        $members[] = $row;
    }

successResponse($members);

