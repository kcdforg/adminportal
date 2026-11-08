<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/reports.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requireReportRole($jwt, ['admin_super', 'admin_accounts', 'admin_readonly']);
[$dateFrom, $dateTo] = reportDateRange();

$conditions = ['1 = 1'];
$params = [];
addReportDateConditions($conditions, $params, 'COALESCE(p.paid_at, p.created_at)', $dateFrom, $dateTo);
$allowedTypes = ['class_fee', 'donation', 'event_fee', 'refund'];
$allowedStatuses = ['pending', 'completed', 'failed', 'refunded'];
foreach (['payment_type' => $allowedTypes, 'status' => $allowedStatuses] as $field => $allowed) {
    $value = trim((string) ($_GET[$field] ?? ''));
    if ($value !== '') {
        if (!in_array($value, $allowed, true)) {
            errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
                $field => ['Select a supported ' . str_replace('_', ' ', $field) . '.'],
            ]);
        }
        $conditions[] = 'p.' . $field . ' = :' . $field;
        $params[':' . $field] = $value;
    }
}

$query = $database->pdo->prepare(
    'SELECT p.id, p.family_id, p.enrollment_id, p.payment_type, p.amount, p.payment_method, p.status, ' .
    'COALESCE(p.paid_at, p.created_at) AS payment_date, p.transaction_reference AS reference_number, p.notes, ' .
    'p.created_at, f.family_name ' .
    'FROM payments p JOIN families f ON f.id = p.family_id WHERE ' . implode(' AND ', $conditions) .
    ' ORDER BY COALESCE(p.paid_at, p.created_at) DESC, p.id DESC'
);
$query->execute($params);
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);
$summary = ['total' => 0.0, 'by_type' => [], 'by_method' => []];
foreach ($transactions as &$payment) {
    $payment['id'] = (int) $payment['id'];
    $payment['family_id'] = (int) $payment['family_id'];
    $payment['enrollment_id'] = $payment['enrollment_id'] === null ? null : (int) $payment['enrollment_id'];
    $payment['amount'] = (float) $payment['amount'];
    $payment['family'] = ['family_name' => $payment['family_name']];
    unset($payment['family_name']);
    $summary['total'] += $payment['amount'];
    $summary['by_type'][$payment['payment_type']] = ($summary['by_type'][$payment['payment_type']] ?? 0) + $payment['amount'];
    $summary['by_method'][$payment['payment_method']] = ($summary['by_method'][$payment['payment_method']] ?? 0) + $payment['amount'];
}
unset($payment);

successResponse(['summary' => $summary, 'transactions' => $transactions]);
