<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/payments.php';
$familyId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!recordExists($database, 'families', ['id' => $familyId])) {
    errorResponse('NOT_FOUND', 'Family not found.', 404);
}
if (!canViewPayments($database, $jwt, $familyId)) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to view payments for this family.', 403);
}
$result = paginatePayments($database, $_GET, $familyId);
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

