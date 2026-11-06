<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';

$database = $bootstrap['database'];
$jwt = checkAuth();
$groupId = (int) ($_GET['group_id'] ?? 0);
if ($groupId > 0) {
    $group = findOne($database, 'parent_groups', ['id' => $groupId]);
    if ($group === null) {
        errorResponse('NOT_FOUND', 'Group not found.', 404);
    }
    if (!canAddGroupMember($database, $jwt, $group, 'member')) {
        errorResponse('UNAUTHORIZED', 'You do not have permission to add group members.', 403);
    }
} else {
    requireGroupAdmin($jwt, 'Only platform admins can view member candidates without a group context.');
}

$search = trim((string) ($_GET['search'] ?? ''));
$perPage = min(max((int) ($_GET['per_page'] ?? 100), 1), 100);
$page = max((int) ($_GET['page'] ?? 1), 1);
$searchSql = '';
$parameters = [];
if ($search !== '') {
    $searchSql = ' AND (mp.first_name LIKE :first_name OR mp.last_name LIKE :last_name OR mp.email LIKE :email OR mp.mobile LIKE :mobile)';
    foreach ([':first_name', ':last_name', ':email', ':mobile'] as $key) {
        $parameters[$key] = '%' . $search . '%';
    }
}
$baseSql = " FROM member_profiles mp
    INNER JOIN user_logins ul ON ul.profile_id = mp.id
        AND ul.user_type = 'member' AND ul.is_active = 1
    INNER JOIN family_members fm ON fm.profile_id = mp.id
        AND fm.status = 'active' AND fm.member_role IN ('primary', 'normal')
    WHERE mp.status = 'active' $searchSql";
$count = $database->pdo->prepare('SELECT COUNT(DISTINCT mp.id)' . $baseSql);
$count->execute($parameters);
$total = (int) $count->fetchColumn();

$query = $database->pdo->prepare(
    'SELECT DISTINCT mp.id, mp.first_name, mp.last_name, mp.email, mp.mobile'
    . $baseSql
    . ' ORDER BY mp.first_name, mp.last_name LIMIT :limit OFFSET :offset'
);
foreach ($parameters as $key => $value) {
    $query->bindValue($key, $value, PDO::PARAM_STR);
}
$query->bindValue(':limit', $perPage, PDO::PARAM_INT);
$query->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT);
$query->execute();
$candidates = array_map('castIds', $query->fetchAll(PDO::FETCH_ASSOC));

jsonResponse([
    'success' => true,
    'data' => $candidates,
    'meta' => [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'last_page' => max(1, (int) ceil($total / $perPage)),
    ],
]);
