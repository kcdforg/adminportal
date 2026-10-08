<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);

$profile = getProfile($database, $id);
if ($profile === null) {
        errorResponse('NOT_FOUND', 'Member profile not found.', 404);
    }

requirePermission(canAccessMember($database, $jwt, $id, 'view'));
successResponse($profile);

