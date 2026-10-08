<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);

$family = getFamily($database, $id);
if ($family === null) {
        errorResponse('NOT_FOUND', 'Family not found.', 404);
    }

requirePermission(canAccessFamily($database, $jwt, $id, 'view'));
successResponse($family);

