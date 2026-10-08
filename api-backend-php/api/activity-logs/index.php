<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!in_array('admin_super', getUserRoles($jwt), true)) {
    errorResponse('UNAUTHORIZED', 'Only super admins can view activity logs.', 403);
}
$where = [];
foreach (['actor_profile_id', 'entity_id'] as $field) {
    if (!empty($_GET[$field])) $where[$field] = (int) $_GET[$field];
}
foreach (['entity_type', 'action'] as $field) {
    if (!empty($_GET[$field])) $where[$field] = $_GET[$field];
}
if (!empty($_GET['created_at_from'])) $where['created_at[>=]'] = $_GET['created_at_from'];
if (!empty($_GET['created_at_to'])) $where['created_at[<=]'] = $_GET['created_at_to'];
$total = (int) $database->count('activity_logs', $where);
$perPage = min(max((int) ($_GET['per_page'] ?? 50), 1), 200);
$page = max((int) ($_GET['page'] ?? 1), 1);
$rows = $database->select('activity_logs', '*', array_merge($where, [
    'ORDER' => ['created_at' => 'DESC'],
    'LIMIT' => [($page - 1) * $perPage, $perPage],
])) ?? [];
foreach ($rows as &$row) {
    $row = castIds($row);
    $actor = !empty($row['actor_profile_id'])
        ? getProfile($database, (int) $row['actor_profile_id'])
        : null;
    $row['actor_name'] = $actor === null ? null : trim($actor['first_name'] . ' ' . $actor['last_name']);
    foreach (['old_values', 'new_values'] as $field) {
        if (isset($row[$field]) && is_string($row[$field])) {
            $decoded = json_decode($row[$field], true);
            if (json_last_error() === JSON_ERROR_NONE) $row[$field] = $decoded;
        }
    }
}
unset($row);
jsonResponse(['success' => true, 'data' => $rows, 'meta' => [
    'total' => $total,
    'per_page' => $perPage,
    'current_page' => $page,
    'last_page' => max(1, (int) ceil($total / $perPage)),
]]);

