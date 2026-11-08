<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/reports.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requireReportRole($jwt, ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']);
[$dateFrom, $dateTo] = reportDateRange();

$conditions = ['1 = 1'];
$params = [];
addReportDateConditions($conditions, $params, 'e.enrolled_at', $dateFrom, $dateTo);
foreach (['batch_id' => 'e.batch_id', 'status' => 'e.status'] as $field => $column) {
    $value = trim((string) ($_GET[$field] ?? ''));
    if ($value !== '') {
        if ($field === 'batch_id' && (!ctype_digit($value) || (int) $value < 1)) {
            errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
                $field => ['Select a valid batch.'],
            ]);
        }
        $conditions[] = $column . ' = :' . $field;
        $params[':' . $field] = $field === 'batch_id' ? (int) $value : $value;
    }
}

$query = $database->pdo->prepare(
    'SELECT e.id, e.family_id, e.member_id, e.batch_id, e.enrolled_at, e.status, e.payment_status, ' .
    'mp.first_name, mp.last_name, b.batch_name, f.family_name ' .
    'FROM enrollments e ' .
    'JOIN member_profiles mp ON mp.id = e.member_id ' .
    'JOIN student_batches b ON b.id = e.batch_id ' .
    'JOIN families f ON f.id = e.family_id ' .
    'WHERE ' . implode(' AND ', $conditions) . ' ORDER BY e.enrolled_at DESC, e.id DESC'
);
$query->execute($params);
$enrollments = $query->fetchAll(PDO::FETCH_ASSOC);
$summary = ['by_status' => [], 'by_payment_status' => [], 'total' => count($enrollments)];
foreach ($enrollments as &$enrollment) {
    foreach (['id', 'family_id', 'member_id', 'batch_id'] as $idField) {
        $enrollment[$idField] = (int) $enrollment[$idField];
    }
    $enrollment['member'] = [
        'first_name' => $enrollment['first_name'],
        'last_name' => $enrollment['last_name'],
    ];
    $enrollment['batch'] = ['batch_name' => $enrollment['batch_name']];
    $enrollment['family'] = ['family_name' => $enrollment['family_name']];
    unset($enrollment['first_name'], $enrollment['last_name'], $enrollment['batch_name'], $enrollment['family_name']);
    $summary['by_status'][$enrollment['status']] = ($summary['by_status'][$enrollment['status']] ?? 0) + 1;
    $summary['by_payment_status'][$enrollment['payment_status']] = ($summary['by_payment_status'][$enrollment['payment_status']] ?? 0) + 1;
}
unset($enrollment);

successResponse(['summary' => $summary, 'enrollments' => $enrollments]);
