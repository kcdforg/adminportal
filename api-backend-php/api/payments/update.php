<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/payments.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isAccountsAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to update payments.', 403);
}
$current = getPayment($database->pdo, $id);
if ($current === null) {
    errorResponse('NOT_FOUND', 'Payment not found.', 404);
}
try {
    assertPaymentEditable($current);
} catch (KcdfPaymentException $exception) {
    handlePaymentError($exception);
}
$body = getJsonBody();
validateOrFail(validatePaymentUpdate($body));
$actorId = getActorId($jwt);
try {
    paymentTransaction($database, static function (PDO $pdo) use ($database, $jwt, $id, $body, $actorId): void {
        $payment = lockPaymentRow($pdo, 'payments', $id, $database->type === 'sqlite');
        if ($payment === null) {
            failPayment('NOT_FOUND', 'Payment not found.', 404);
        }
        assertPaymentEditable($payment);

        $updates = getPaymentUpdateValues($body, $payment, date('Y-m-d H:i:s'));
        if ($updates !== []) {
            $set = [];
            $params = [':id' => $id];
            foreach ($updates as $field => $value) {
                $set[] = $field . ' = :' . $field;
                $params[':' . $field] = $value;
            }
            $statement = $pdo->prepare('UPDATE payments SET ' . implode(', ', $set) . ' WHERE id = :id');
            $statement->execute($params);
        }
        if ($payment['enrollment_id'] !== null) {
            recalculateEnrollment($pdo, (int) $payment['enrollment_id'], $database->type === 'sqlite');
        }
        $updated = getPayment($pdo, $id);
        logActivity($database, ['profile_id' => $actorId], 'payment_updated', 'payments', $id, ['status' => $payment['status']], [
            'amount' => $updated['amount'],
            'payment_type' => $updated['payment_type'],
            'status' => $updated['status'],
            'family_id' => $updated['family_id'],
        ]);
    });
} catch (KcdfPaymentException $exception) {
    handlePaymentError($exception);
}
successResponse(getPayment($database->pdo, $id), 'Payment updated successfully.');

