<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$filters = $_GET;
$filters['user_type'] = 'admin';
if (!isset($filters['role']) && isset($filters['admin_role'])) {
    $filters['role'] = $filters['admin_role'];
}
if (($filters['sort'] ?? null) === 'admin_role') {
    $filters['sort'] = 'role';
}
if (isset($filters['status'])) {
    if ($filters['status'] === 'active') {
        $filters['is_active'] = 1;
    } elseif ($filters['status'] === 'inactive') {
        $filters['is_active'] = 0;
    }
}
$result = paginate(
    $database,
    'user_logins',
    $filters,
    ['user_type' => 'user_type', 'is_active' => 'is_active', 'role' => 'role'],
    ['id', 'role', 'created_at'],
    ['username']
);
$result['data'] = array_values(array_filter(array_map(
    static fn (array $login): ?array => getAdmin($database, (int) $login['id']),
    $result['data']
)));
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);
