<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$result = paginate(
    $database,
    'admins',
    $_GET,
    ['status' => 'status', 'admin_role' => 'admin_role'],
    ['id', 'admin_role', 'created_at']
);
foreach ($result['data'] as &$admin) {
    $admin['profile'] = getProfile($database, (int) $admin['profile_id']);
}
unset($admin);
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

