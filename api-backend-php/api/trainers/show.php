<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);
$trainer = getTrainerRecord($database, $id);
if ($trainer === null) {
    errorResponse('NOT_FOUND', 'Trainer not found.', 404);
}
$isOwn = getUserId($jwt) === (int) $trainer['profile_id']
&& in_array('trainer', getUserRoles($jwt), true);
requirePermission(isAdmin($jwt) || $isOwn);
successResponse($trainer);

