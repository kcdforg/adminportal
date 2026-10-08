<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/payments.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$body = getJsonBody();
validateOrFail(validatePaymentCreate($body));
if (!isAccountsAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'Only accounts admins and super admins can create payments.', 403);
}
$familyId = (int) $body['family_id'];
$enrollmentId = !empty($body['enrollment_id']) ? (int) $body['enrollment_id'] : null;
$actorId = getActorId($jwt);
try {
    $paymentId = paymentTransaction($database, static function (PDO $pdo) use ($database, $body, $familyId, $enrollmentId, $actorId): int {
        $familyStatement = $pdo->prepare('SELECT id, status FROM families WHERE id = :id');
        $familyStatement->execute([':id' => $familyId]);
        $family = $familyStatement->fetch(PDO::FETCH_ASSOC);
        assertFamilyActive(is_array($family) ? $family : null);
        if ($enrollmentId !== null) {
            $enrollment = lockPaymentRow($pdo, 'enrollments', $enrollmentId, $database->type === 'sqlite');
            assertEnrollmentFamily($enrollment, $familyId);
        }

        $paidAt = $body['status'] === 'completed' ? date('Y-m-d H:i:s') : null;
        $insert = $pdo->prepare(
            'INSERT INTO payments ' .
            '(family_id, enrollment_id, payment_type, amount, payment_method, transaction_reference, status, notes, paid_at) ' .
            'VALUES (:family_id, :enrollment_id, :payment_type, :amount, :payment_method, :transaction_reference, :status, :notes, :paid_at)'
        );
        $insert->execute([
            ':family_id' => $familyId,
            ':enrollment_id' => $enrollmentId,
            ':payment_type' => $body['payment_type'],
            ':amount' => number_format((float) $body['amount'], 2, '.', ''),
            ':payment_method' => $body['payment_method'],
            ':transaction_reference' => !empty($body['transaction_reference']) ? $body['transaction_reference'] : null,
            ':status' => $body['status'],
            ':notes' => !empty($body['notes']) ? $body['notes'] : null,
            ':paid_at' => $paidAt,
        ]);
        $id = (int) $pdo->lastInsertId();
        if ($enrollmentId !== null) {
            recalculateEnrollment($pdo, $enrollmentId, $database->type === 'sqlite');
        }
        $payment = getPayment($pdo, $id);
        logActivity($database, ['profile_id' => $actorId], 'payment_recorded', 'payments', $id, null, [
            'amount' => $payment['amount'],
            'payment_type' => $payment['payment_type'],
            'status' => $payment['status'],
            'family_id' => $payment['family_id'],
        ]);
        return $id;
    });
} catch (KcdfPaymentException $exception) {
    handlePaymentError($exception);
}
successResponse(getPayment($database->pdo, $paymentId), 'Payment recorded successfully.', 201);

