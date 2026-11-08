<?php

declare(strict_types=1);

require_once __DIR__ . '/identity.php';

function requireReportRole(array $jwt, array $allowedRoles): void
{
    if (array_intersect(getUserRoles($jwt), $allowedRoles) === []) {
        errorResponse('UNAUTHORIZED', 'You are not authorized to view this report.', 403);
    }
}

function reportDateRange(): array
{
    $dateFrom = trim((string) ($_GET['date_from'] ?? ''));
    $dateTo = trim((string) ($_GET['date_to'] ?? ''));
    $errors = [];

    if ($dateFrom !== '' && !isValidDate($dateFrom)) {
        $errors['date_from'] = ['Enter a valid date in YYYY-MM-DD format.'];
    }
    if ($dateTo !== '' && !isValidDate($dateTo)) {
        $errors['date_to'] = ['Enter a valid date in YYYY-MM-DD format.'];
    }
    if ($dateFrom !== '' && $dateTo !== '' && $dateFrom > $dateTo) {
        $errors['date_to'] = ['The end date must be on or after the start date.'];
    }
    validateOrFail($errors);

    return [$dateFrom, $dateTo];
}

function reportPositiveId(string $field): int
{
    $value = $_GET[$field] ?? null;
    if (!is_scalar($value) || !ctype_digit((string) $value) || (int) $value < 1) {
        errorResponse('VALIDATION_FAILED', 'Validation failed', 422, [
            $field => ['Select a valid ' . str_replace('_', ' ', $field) . '.'],
        ]);
    }
    return (int) $value;
}

function addReportDateConditions(array &$conditions, array &$params, string $column, string $dateFrom, string $dateTo): void
{
    if ($dateFrom !== '') {
        $conditions[] = 'DATE(' . $column . ') >= :date_from';
        $params[':date_from'] = $dateFrom;
    }
    if ($dateTo !== '') {
        $conditions[] = 'DATE(' . $column . ') <= :date_to';
        $params[':date_to'] = $dateTo;
    }
}
