<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(in_array('admin_super', getUserRoles($jwt), true));
$admin = getAdmin($database, $id);
if ($admin === null) {
    errorResponse('NOT_FOUND', 'Admin not found.', 404);
}
successResponse($admin);

