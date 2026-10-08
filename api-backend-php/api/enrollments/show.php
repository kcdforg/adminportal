<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$enrollment = getEnrollment($database, $id, true);
if ($enrollment === null) errorResponse('NOT_FOUND', 'Enrollment not found.', 404);
requirePermission(canAccessEnrollment($database, $jwt, $enrollment, 'view'));
successResponse($enrollment);

