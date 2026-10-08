<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

$session = getSession($database, $id);
if ($session === null) {
        errorResponse('NOT_FOUND', 'Session not found.', 404);
    }

requirePermission(canAccessSession($database, $jwt, $session, $session['batch']));
successResponse($session);

