<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

$program = getProgram($database, $id);
if ($program === null) {
        errorResponse('NOT_FOUND', 'Program not found.', 404);
    }

successResponse($program);

