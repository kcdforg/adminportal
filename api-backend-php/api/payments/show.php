<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/payments.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$payment = getPayment($database->pdo, $id);
if ($payment === null) {
    errorResponse('NOT_FOUND', 'Payment not found.', 404);
}
if (!canViewPayments($database, $jwt, (int) $payment['family_id'])) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to view this payment.', 403);
}
successResponse($payment);

