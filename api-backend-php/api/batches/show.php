<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

$batch = getBatch($database, $id);
if ($batch === null) {
        errorResponse('NOT_FOUND', 'Batch not found.', 404);
    }

requirePermission(canAccessBatch($database, $jwt, $batch));
successResponse($batch);

