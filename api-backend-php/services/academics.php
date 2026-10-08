<?php

declare(strict_types=1);

require_once __DIR__ . '/identity.php';

final class KcdfAcademicsException extends RuntimeException
{
    public function __construct(public readonly string $errorCode, string $message, public readonly int $status = 422, public readonly array $details = [])
    {
        parent::__construct($message);
    }
}

function failAcademics(string $code, string $message, int $status = 422, array $details = []): never
{
    throw new KcdfAcademicsException($code, $message, $status, $details);
}

function handleAcademicsError(KcdfAcademicsException $exception): never
{
    errorResponse($exception->errorCode, $exception->getMessage(), $exception->status, $exception->details);
}

function isTrainer(array $jwt): bool
{
    return in_array('trainer', getUserRoles($jwt), true);
}

function hasElevatedAccess(array $jwt): bool
{
    return isElevatedAdmin($jwt);
}

function getTrainerId(Medoo\Medoo $database, array $jwt): ?int
{
    $profileId = getUserId($jwt);
    if ($profileId < 1) {
        return null;
    }
    $id = $database->get('trainers', 'id', ['profile_id' => $profileId]);
    return $id === null ? null : (int) $id;
}

function isMemberInBatch(Medoo\Medoo $database, int $memberId, int $batchId): bool
{
    return $database->has('enrollments', [
        'member_id' => $memberId,
        'batch_id' => $batchId,
        'status' => ['active', 'pending'],
    ]);
}

function canViewBatch(Medoo\Medoo $database, array $jwt, array $batch): bool
{
    if (isAdmin($jwt)) {
        return true;
    }

    $trainerId = getTrainerId($database, $jwt);
    if (isTrainer($jwt) && $trainerId !== null && $trainerId === (int) ($batch['trainer_id'] ?? 0)) {
        return true;
    }

    return isMemberInBatch($database, getUserId($jwt), (int) $batch['id']);
}

function canManageBatchMembers(Medoo\Medoo $database, array $jwt, array $batch): bool
{
    if (!isTrainer($jwt)) {
        return false;
    }

    return getTrainerId($database, $jwt) === (int) ($batch['trainer_id'] ?? 0);
}

function canViewSession(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null): bool
{
    if (isAdmin($jwt)) {
        return true;
    }

    if (isTrainer($jwt) && isSessionTrainer($database, $jwt, $session, $batch)) {
        return true;
    }

    return isMemberInBatch($database, getUserId($jwt), (int) $session['batch_id']);
}

function canEditSession(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null): bool
{
    if (hasElevatedAccess($jwt)) {
        return true;
    }

    return isTrainer($jwt)
        && isSessionTrainer($database, $jwt, $session, $batch);
}

function canManageAttendance(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null): bool
{
    return hasElevatedAccess($jwt)
        || (isTrainer($jwt) && isSessionTrainer($database, $jwt, $session, $batch));
}

function canAccessBatch(Medoo\Medoo $database, array $jwt, array $batch, string $scope = 'view'): bool
{
    return match ($scope) {
        'members' => canManageBatchMembers($database, $jwt, $batch),
        default => canViewBatch($database, $jwt, $batch),
    };
}

function isSessionTrainer(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null): bool
{
    $trainerId = getTrainerId($database, $jwt);
    $effective = $session['trainer_id'] ?? ($batch['trainer_id'] ?? null);
    return $trainerId !== null && $effective !== null && $trainerId === (int) $effective;
}

function canAccessSession(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null, string $scope = 'view'): bool
{
    return match ($scope) {
        'view' => canViewSession($database, $jwt, $session, $batch),
        'edit' => canEditSession($database, $jwt, $session, $batch),
        default => canViewSession($database, $jwt, $session, $batch),
    };
}

function canAccessAttendance(Medoo\Medoo $database, array $jwt, array $session, ?array $batch = null): bool
{
    return canManageAttendance($database, $jwt, $session, $batch);
}

function paginateAcademics(Medoo\Medoo $database, string $table, array $filters, array $filterColumns, array $sortColumns, string $defaultSort, array $extraWhere = []): array
{
    $where = [];
    foreach ($filterColumns as $input => $column) {
        if (isset($filters[$input]) && $filters[$input] !== '') {
            $where[$column] = $filters[$input];
        }
    }
    $where = array_merge($where, $extraWhere);
    $total = (int) $database->count($table, $where);
    $perPage = min(max((int) ($filters['per_page'] ?? 20), 1), 100);
    $page = max((int) ($filters['page'] ?? 1), 1);
    $sort = in_array($filters['sort'] ?? '', $sortColumns, true) ? $filters['sort'] : $defaultSort;
    $order = strtolower((string) ($filters['order'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
    $rows = $database->select($table, '*', array_merge($where, [
        'ORDER' => [$sort => $order],
        'LIMIT' => [($page - 1) * $perPage, $perPage],
    ])) ?? [];
    return [
        'data' => array_map('castIds', $rows),
        'meta' => ['total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1, (int) ceil($total / $perPage))],
    ];
}

function emptyPage(array $filters): array
{
    $perPage = min(max((int) ($filters['per_page'] ?? 20), 1), 100);
    return ['data' => [], 'meta' => ['total' => 0, 'per_page' => $perPage, 'current_page' => 1, 'last_page' => 1]];
}

function getProgram(Medoo\Medoo $database, int $id): ?array
{
    return findOne($database, 'programs', ['id' => $id]);
}

function getBatch(Medoo\Medoo $database, int $id, bool $relations = true): ?array
{
    $row = findOne($database, 'student_batches', ['id' => $id]);
    if ($row !== null && $relations) {
        $row['program'] = getProgram($database, (int) $row['program_id']);
        $row['trainer'] = getTrainerRecord($database, (int) ($row['trainer_id'] ?? 0));
    }
    return $row;
}

function getAcademicTrainer(Medoo\Medoo $database, int $id): ?array
{
    if ($id < 1) {
        return null;
    }
    return getTrainerRecord($database, $id);
}

function getSession(Medoo\Medoo $database, int $id, bool $relations = true): ?array
{
    $row = findOne($database, 'batch_sessions', ['id' => $id]);
    if ($row !== null && $relations) {
        $row['batch'] = getBatch($database, (int) $row['batch_id']);
        $row['trainer'] = getTrainerRecord($database, (int) ($row['trainer_id'] ?? 0));
        $row['attendance_locked'] = (bool) $row['attendance_locked'];
    }
    return $row;
}

function validateProgram(array $data, string $mode): array
{
    $errors = [];
    $types = ['class', 'workshop', 'camp', 'event'];
    $statuses = ['active', 'inactive', 'archived'];
    if ($mode === 'create') {
        validateRequired($errors, 'program_name', $data['program_name'] ?? null, 'The program_name field is required.');
        validateRequired($errors, 'program_type', $data['program_type'] ?? null, 'The program_type field is required.');
        if (!array_key_exists('fee_amount', $data)) {
            $errors['fee_amount'] = ['The fee_amount field is required.'];
        }
    }
    if (array_key_exists('program_name', $data)) {
        validateTextField($errors, 'program_name', $data['program_name'], 255, 'The program_name field is required.', 'The program_name field cannot be empty.', 'The program_name may not be greater than 255 characters.', $mode === 'create');
    }
    validateChoice($errors, 'program_type', $data['program_type'] ?? null, $types, 'The program_type must be one of: class, workshop, camp, event.');
    if (array_key_exists('fee_amount', $data)) {
        validateNonNegativeNumber($errors, 'fee_amount', $data['fee_amount'], 'The fee_amount must be a non-negative number.', 'The fee_amount may not have more than 2 decimal places.');
    }
    if ($mode === 'status') {
        validateRequired($errors, 'status', $data['status'] ?? null, 'The status field is required.');
    }
    validateChoice($errors, 'status', $data['status'] ?? null, $statuses, 'The status must be one of: active, inactive, archived.');
    return $errors;
}

function validateBatch(array $data, string $mode): array
{
    $errors = [];
    $statuses = ['upcoming', 'active', 'completed', 'cancelled'];
    if ($mode === 'create') {
        validateRequired($errors, 'program_id', $data['program_id'] ?? null, 'The program_id field is required.');
        if (isset($data['program_id']) && $data['program_id'] !== '') {
            validatePositiveInt($errors, 'program_id', $data['program_id'], 'The program_id must be a valid integer.');
        }
        validateRequired($errors, 'batch_name', $data['batch_name'] ?? null, 'The batch_name field is required.');
    }
    if (array_key_exists('batch_name', $data)) {
        validateTextField($errors, 'batch_name', $data['batch_name'], 255, 'The batch_name field is required.', 'The batch_name field cannot be empty.', 'The batch_name may not be greater than 255 characters.', $mode === 'create');
    }
    if (isset($data['capacity']) && (!is_numeric($data['capacity']) || (int) $data['capacity'] < 1)) {
        $errors['capacity'] = ['The capacity must be an integer of at least 1.'];
    }
    validateChoice($errors, 'status', $data['status'] ?? null, $statuses, 'The status must be one of: upcoming, active, completed, cancelled.');
    foreach (['start_date', 'end_date'] as $field) {
        if (!empty($data[$field])) {
            validateDate($errors, $field, $data[$field], "The {$field} must be a valid date (YYYY-MM-DD).") ;
        }
    }
    if (!empty($data['end_date']) && !empty($data['start_date']) && isValidDate((string) $data['start_date']) && $data['end_date'] <= $data['start_date']) {
        $errors['end_date'] = ['The end_date must be after the start_date.'];
    }
    return $errors;
}

function validateSession(array $data, string $mode): array
{
    $errors = [];
    $types = ['regular', 'special', 'exam', 'workshop'];
    $statuses = ['scheduled', 'completed', 'cancelled', 'postponed'];
    if ($mode === 'create') {
        validateRequired($errors, 'session_date', $data['session_date'] ?? null, 'The session_date field is required.');
        validateRequired($errors, 'session_type', $data['session_type'] ?? null, 'The session_type field is required.');
    }
    if (!empty($data['session_date'])) {
        validateDate($errors, 'session_date', $data['session_date'], 'The session_date must be a valid date (YYYY-MM-DD).');
    }
    validateChoice($errors, 'session_type', $data['session_type'] ?? null, $types, 'The session_type must be one of: regular, special, exam, workshop.');
    validateChoice($errors, 'status', $data['status'] ?? null, $statuses, 'The status must be one of: scheduled, completed, cancelled, postponed.');
    foreach (['start_time', 'end_time'] as $field) {
        if (!empty($data[$field])) {
            validateTime($errors, $field, $data[$field], "The {$field} must be a valid time (HH:MM).") ;
        }
    }
    if (!empty($data['end_time']) && !empty($data['start_time']) && $data['end_time'] <= $data['start_time']) {
        $errors['end_time'] = ['The end_time must be after the start_time.'];
    }
    return $errors;
}

function academicsTransaction(Medoo\Medoo $database, callable $callback)
{
    $pdo = $database->pdo;
    $sqlite = $database->type === 'sqlite';
    if ($sqlite) {
        $pdo->exec('BEGIN IMMEDIATE');
    } else {
        $pdo->beginTransaction();
    }
    try {
        $result = $callback();
        if ($sqlite) {
            $pdo->exec('COMMIT');
        } else {
            $pdo->commit();
        }
        return $result;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            if ($sqlite) $pdo->exec('ROLLBACK');
            else $pdo->rollBack();
        }
        throw $exception;
    }
}

function lockBatch(Medoo\Medoo $database, int $id): ?array
{
    if ($database->type === 'sqlite') {
        return getBatch($database, $id, false);
    }
    $statement = $database->pdo->prepare('SELECT * FROM student_batches WHERE id = :id FOR UPDATE');
    $statement->execute([':id' => $id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? castIds($row) : null;
}

function lockSession(Medoo\Medoo $database, int $id): ?array
{
    if ($database->type === 'sqlite') {
        return findOne($database, 'batch_sessions', ['id' => $id]);
    }
    $statement = $database->pdo->prepare('SELECT * FROM batch_sessions WHERE id = :id FOR UPDATE');
    $statement->execute([':id' => $id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? castIds($row) : null;
}

function lockEnrollment(Medoo\Medoo $database, int $id): ?array
{
    if ($database->type === 'sqlite') {
        return findOne($database, 'enrollments', ['id' => $id]);
    }
    $statement = $database->pdo->prepare('SELECT * FROM enrollments WHERE id = :id FOR UPDATE');
    $statement->execute([':id' => $id]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? castIds($row) : null;
}

function getAcademicProfile(Medoo\Medoo $database, ?int $id): ?array
{
    return $id ? getProfile($database, $id) : null;
}

function getEnrollment(Medoo\Medoo $database, int $id, bool $includeEnrolledBy = false): ?array
{
    $row = findOne($database, 'enrollments', ['id' => $id]);
    if ($row === null) {
        return null;
    }
    $row['family'] = getFamily($database, (int) $row['family_id']);
    $row['member'] = getAcademicProfile($database, (int) $row['member_id']);
    $row['batch'] = getBatch($database, (int) $row['batch_id']);
    if ($includeEnrolledBy) {
        $row['enrolled_by'] = getAcademicProfile($database, isset($row['enrolled_by_member_id']) ? (int) $row['enrolled_by_member_id'] : null);
    }
    return $row;
}

function isPrimaryInFamily(Medoo\Medoo $database, int $profileId, int $familyId): bool
{
    return $database->has('family_members', [
        'family_id' => $familyId,
        'profile_id' => $profileId,
        'member_role' => 'primary',
        'status' => 'active',
    ]);
}

function canAccessEnrollment(Medoo\Medoo $database, array $jwt, array $enrollment, string $scope): bool
{
    if (isAdmin($jwt)) {
        return true;
    }
    if ($scope === 'view' && isPrimaryInFamily($database, getUserId($jwt), (int) $enrollment['family_id'])) {
        return true;
    }
    if ($scope === 'view') {
        return (int) $enrollment['member_id'] === getUserId($jwt);
    }
    return $scope === 'cancel' && isPrimaryInFamily($database, getUserId($jwt), (int) $enrollment['family_id']);
}
