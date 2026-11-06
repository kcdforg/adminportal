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

if (!canAccessGroup($database, $jwt, $group, 'members_view')) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to view members of this group.', 403);
    }

$includeInactive = ($_GET['include_inactive'] ?? null) === '1';
if ($includeInactive && !isCommunityAdmin($jwt)) {
        errorResponse('UNAUTHORIZED', 'Only platform admins can view inactive group memberships.', 403);
    }
$memberStatusFilter = $includeInactive ? null : 'active';
$where = ['group_members.group_id' => $id];
if ($memberStatusFilter !== null) {
        $where['group_members.status'] = $memberStatusFilter;
    }
$rows = $database->select('group_members', ['[>]member_profiles' => ['member_id' => 'id']], [
        'group_members.id',
        'group_members.group_id',
        'group_members.member_id',
        'group_members.joined_at',
        'group_members.status',
        'group_members.member_role',
        'member_profiles.first_name',
        'member_profiles.last_name',
        'member_profiles.mobile',
        'member_profiles.email',
    ], array_merge($where, ['ORDER' => ['group_members.joined_at' => 'DESC']])) ?? [];

$members = [];
foreach ($rows as $row) {
        $row = castIds($row);
        $row['member_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
        unset($row['first_name'], $row['last_name']);
        $members[] = $row;
    }

successResponse($members);
