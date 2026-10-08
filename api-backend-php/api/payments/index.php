<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/payments.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isAccountsAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to view all payments.', 403);
}
$result = paginatePayments($database, $_GET);
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

