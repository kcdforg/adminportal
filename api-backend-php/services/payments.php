<?php

declare(strict_types=1);

require_once __DIR__ . '/identity.php';

final class KcdfPaymentException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $status = 422,
        public readonly array $details = []
    ) {
        parent::__construct($message);
    }
}

function failPayment(string $code, string $message, int $status = 422, array $details = []): never
{
    throw new KcdfPaymentException($code, $message, $status, $details);
}

function handlePaymentError(KcdfPaymentException $exception): never
{
    errorResponse($exception->errorCode, $exception->getMessage(), $exception->status, $exception->details);
}

function isAccountsAdmin(array $jwt): bool
{
    return count(array_intersect(getUserRoles($jwt), ['admin_super', 'admin_accounts'])) > 0;
}

function isPaymentAdmin(array $jwt): bool
{
    return isAdmin($jwt);
}

function isPrimaryFamilyMember(Medoo\Medoo $database, array $jwt, int $familyId): bool
{
    $profileId = getUserId($jwt);
    return $profileId > 0 && $database->has('family_members', [
        'family_id' => $familyId,
        'profile_id' => $profileId,
        'member_role' => 'primary',
        'status' => 'active',
    ]);
}

function canViewPayments(Medoo\Medoo $database, array $jwt, int $familyId): bool
{
    return isPaymentAdmin($jwt) || isPrimaryFamilyMember($database, $jwt, $familyId);
}

function assertFamilyActive(?array $family): void
{
    if ($family === null) {
        failPayment('NOT_FOUND', 'Family not found.', 404);
    }
    if (($family['status'] ?? null) !== 'active') {
        failPayment('NOT_FOUND', 'Family is not active.', 404);
    }
}

function assertEnrollmentFamily(?array $enrollment, int $familyId): void
{
    if ($enrollment === null) {
        failPayment('NOT_FOUND', 'Enrollment not found.', 404);
    }
    if ((int) ($enrollment['family_id'] ?? 0) !== $familyId) {
        failPayment('NOT_FOUND', 'Enrollment does not belong to the specified family.', 404);
    }
}

function assertPaymentEditable(array $payment): void
{
    if (($payment['status'] ?? null) === 'completed') {
        failPayment('PAYMENT_COMPLETED', 'A completed payment cannot be edited. Create a refund payment instead.');
    }
}

function getPaymentUpdateValues(array $data, array $payment, string $completedAt): array
{
    $updates = [];
    if (isset($data['status'])) {
        $updates['status'] = $data['status'];
        if ($data['status'] === 'completed' && ($payment['paid_at'] ?? null) === null) {
            $updates['paid_at'] = $completedAt;
        }
    }
    foreach (['transaction_reference', 'notes'] as $field) {
        if (array_key_exists($field, $data)) {
            $updates[$field] = $data[$field];
        }
    }
    return $updates;
}

function validatePaymentCreate(array $data): array
{
    $errors = [];
    $types = ['class_fee', 'donation', 'event_fee', 'refund'];
    $methods = ['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'online'];
    $statuses = ['pending', 'completed', 'failed'];
    if (empty($data['family_id'])) {
        $errors['family_id'] = ['The family_id field is required.'];
    } elseif (!is_numeric($data['family_id'])) {
        $errors['family_id'] = ['The family_id must be a valid integer.'];
    }
    if (empty($data['payment_type'])) {
        $errors['payment_type'] = ['The payment_type field is required.'];
    } elseif (!in_array($data['payment_type'], $types, true)) {
        $errors['payment_type'] = ['The payment_type must be one of: ' . implode(', ', $types) . '.'];
    }
    $paymentType = $data['payment_type'] ?? '';
    if ($paymentType === 'class_fee') {
        if (empty($data['enrollment_id'])) {
            $errors['enrollment_id'] = ['The enrollment_id is required for class_fee payments.'];
        } elseif (!is_numeric($data['enrollment_id'])) {
            $errors['enrollment_id'] = ['The enrollment_id must be a valid integer.'];
        }
    } elseif (!empty($data['enrollment_id']) && !is_numeric($data['enrollment_id'])) {
        $errors['enrollment_id'] = ['The enrollment_id must be a valid integer.'];
    }
    if (!isset($data['amount']) || $data['amount'] === '') {
        $errors['amount'] = ['The amount field is required.'];
    } elseif (!is_numeric($data['amount'])) {
        $errors['amount'] = ['The amount must be a numeric value.'];
    } elseif ((float) $data['amount'] < 0.01) {
        $errors['amount'] = ['The amount must be at least 0.01.'];
    } elseif (getDecimalPlaces($data['amount']) > 2) {
        $errors['amount'] = ['The amount must not exceed 2 decimal places.'];
    }
    if (empty($data['payment_method'])) {
        $errors['payment_method'] = ['The payment_method field is required.'];
    } elseif (!in_array($data['payment_method'], $methods, true)) {
        $errors['payment_method'] = ['The payment_method must be one of: ' . implode(', ', $methods) . '.'];
    }
    $method = $data['payment_method'] ?? '';
    if ($method !== 'cash' && in_array($method, $methods, true) && empty($data['transaction_reference'])) {
        $errors['transaction_reference'] = ['The transaction_reference is required for non-cash payments.'];
    }
    if (empty($data['status'])) {
        $errors['status'] = ['The status field is required.'];
    } elseif (!in_array($data['status'], $statuses, true)) {
        $errors['status'] = ['The status must be one of: ' . implode(', ', $statuses) . '.'];
    }
    if (!empty($data['notes']) && strlen((string) $data['notes']) > 1000) {
        $errors['notes'] = ['The notes must not exceed 1000 characters.'];
    }
    return $errors;
}

function validatePaymentUpdate(array $data): array
{
    $errors = [];
    if (isset($data['status']) && !in_array($data['status'], ['pending', 'completed', 'failed'], true)) {
        $errors['status'] = ['The status must be one of: pending, completed, failed.'];
    }
    if (isset($data['notes']) && strlen((string) $data['notes']) > 1000) {
        $errors['notes'] = ['The notes must not exceed 1000 characters.'];
    }
    return $errors;
}

function getDecimalPlaces($value): int
{
    $string = (string) $value;
    $dot = strpos($string, '.');
    return $dot === false ? 0 : strlen($string) - $dot - 1;
}

function paymentTransaction(Medoo\Medoo $database, callable $callback)
{
    $pdo = $database->pdo;
    $sqlite = $database->type === 'sqlite';
    if ($sqlite) {
        $pdo->exec('BEGIN IMMEDIATE');
    } else {
        $pdo->beginTransaction();
    }
    try {
        $result = $callback($pdo);
        if ($sqlite) {
            $pdo->exec('COMMIT');
        } else {
            $pdo->commit();
        }
        return $result;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            if ($sqlite) {
                $pdo->exec('ROLLBACK');
            } else {
                $pdo->rollBack();
            }
        }
        throw $exception;
    }
}

function lockPaymentRow(PDO $pdo, string $table, int $id, bool $sqlite): ?array
{
    if (!in_array($table, ['payments', 'enrollments'], true)) {
        throw new InvalidArgumentException('Unsupported table requested for payment row locking.');
    }
    $sql = 'SELECT * FROM ' . $table . ' WHERE id = :id' . ($sqlite ? '' : ' FOR UPDATE');
    $statement = $pdo->prepare($sql);
    $statement->execute([':id' => $id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? castIds($row) : null;
}

function recalculateEnrollment(PDO $pdo, int $enrollmentId, bool $sqlite): void
{
    $enrollment = lockPaymentRow($pdo, 'enrollments', $enrollmentId, $sqlite);
    if ($enrollment === null) {
        return;
    }
    $statement = $pdo->prepare(
        "SELECT COALESCE(SUM(CASE WHEN payment_type = 'refund' THEN -amount ELSE amount END), 0) " .
        "FROM payments WHERE enrollment_id = :enrollment_id AND status = 'completed'"
    );
    $statement->execute([':enrollment_id' => $enrollmentId]);
    $netPaid = (float) $statement->fetchColumn();
    $feeAmount = (float) $enrollment['fee_amount'];
    if ($netPaid <= 0) {
        $paymentStatus = 'unpaid';
    } elseif ($netPaid < $feeAmount) {
        $paymentStatus = 'partial';
    } else {
        $paymentStatus = 'paid';
    }
    $update = $pdo->prepare('UPDATE enrollments SET payment_status = :payment_status WHERE id = :id');
    $update->execute([':payment_status' => $paymentStatus, ':id' => $enrollmentId]);
}

function getPayment(PDO $pdo, int $id): ?array
{
    $statement = $pdo->prepare('SELECT * FROM payments WHERE id = :id');
    $statement->execute([':id' => $id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    if (!is_array($row)) {
        return null;
    }
    unset($row['updated_at']);
    return formatPayment(castIds($row));
}

function formatPayment(array $row): array
{
    if (array_key_exists('amount', $row)) {
        $row['amount'] = number_format((float) $row['amount'], 2, '.', '');
    }
    return $row;
}

function paginatePayments(Medoo\Medoo $database, array $filters, int $familyId = 0): array
{
    $where = [];
    if ($familyId > 0) {
        $where['family_id'] = $familyId;
    } elseif (!empty($filters['family_id'])) {
        $where['family_id'] = (int) $filters['family_id'];
    }
    foreach (['payment_type', 'status', 'payment_method'] as $field) {
        if (!empty($filters[$field])) {
            $where[$field] = $filters[$field];
        }
    }
    if (!empty($filters['paid_at_from'])) {
        $where['paid_at[>=]'] = $filters['paid_at_from'];
    }
    if (!empty($filters['paid_at_to'])) {
        $where['paid_at[<=]'] = $filters['paid_at_to'];
    }
    $total = (int) $database->count('payments', $where);
    $perPage = min(max((int) ($filters['per_page'] ?? 20), 1), 100);
    $page = max((int) ($filters['page'] ?? 1), 1);
    $rows = $database->select('payments', '*', array_merge($where, [
        'ORDER' => ['created_at' => 'DESC'],
        'LIMIT' => [($page - 1) * $perPage, $perPage],
    ])) ?? [];
    $data = [];
    foreach ($rows as $row) {
        unset($row['updated_at']);
        $data[] = formatPayment(castIds($row));
    }
    return [
        'data' => $data,
        'meta' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ],
    ];
}
