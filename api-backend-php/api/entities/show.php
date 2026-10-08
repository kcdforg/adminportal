<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$entity = getEntity($database, $id);
if ($entity === null || ($entity['status'] !== 'active' && !isAdmin($jwt))) {
    errorResponse('NOT_FOUND', 'Entity not found.', 404);
}
successResponse($entity);

