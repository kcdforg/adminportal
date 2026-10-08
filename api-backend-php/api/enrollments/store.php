<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$body = getJsonBody();
$errors = [];
foreach (['family_id', 'member_id', 'batch_id'] as $field) {
    if (empty($body[$field])) $errors[$field] = ["The {$field} field is required."];
    elseif (!is_numeric($body[$field])) $errors[$field] = ["The {$field} must be a valid integer."];
}
validateOrFail($errors);
$familyId = (int) $body['family_id'];
$memberId = (int) $body['member_id'];
$batchId = (int) $body['batch_id'];
if (!isAdmin($jwt) && !isPrimaryInFamily($database, getUserId($jwt), $familyId)) {
    errorResponse('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
}
if (!recordExists($database, 'families', ['id' => $familyId])) errorResponse('NOT_FOUND', 'Family not found.', 404);
if (!recordExists($database, 'family_members', ['family_id' => $familyId, 'profile_id' => $memberId, 'status' => 'active'])) {
    validateOrFail(['member_id' => ['The member does not belong to the specified family.']]);
}
$actorId = getActorId($jwt);
try {
    $enrollmentId = academicsTransaction($database, static function () use ($database, $familyId, $memberId, $batchId, $actorId): int {
        $batch = lockBatch($database, $batchId);
        if ($batch === null) failAcademics('NOT_FOUND', 'Batch not found.', 404);
        if (!in_array($batch['status'], ['upcoming', 'active'], true)) {
            failAcademics('BATCH_NOT_ENROLLABLE', 'Enrollment is only allowed for upcoming or active batches.');
        }
        if ($database->has('enrollments', ['member_id' => $memberId, 'batch_id' => $batchId])) {
            failAcademics('ENROLLMENT_EXISTS', 'This member is already enrolled in this batch.');
        }
        if ($batch['capacity'] !== null && (int) $database->count('batch_members', ['batch_id' => $batchId, 'status' => 'active']) >= (int) $batch['capacity']) {
            failAcademics('BATCH_FULL', 'This batch has reached its maximum capacity.');
        }
        $program = getProgram($database, (int) $batch['program_id']);
        $fee = $program['fee_amount'] ?? 0;
        $now = date('Y-m-d H:i:s');
        $database->insert('enrollments', [
            'family_id' => $familyId, 'member_id' => $memberId, 'batch_id' => $batchId,
            'enrolled_by_member_id' => $actorId, 'enrolled_at' => $now,
            'status' => 'active', 'payment_status' => 'unpaid', 'fee_amount' => $fee,
        ]);
        $newId = (int) $database->id();
        $database->insert('batch_members', [
            'batch_id' => $batchId, 'member_id' => $memberId, 'joined_at' => $now, 'status' => 'active',
        ]);
        $enrollment = getEnrollment($database, $newId);
        logActivity($database, ['profile_id' => $actorId], 'create', 'enrollments', $newId, null, $enrollment);
        return $newId;
    });
} catch (KcdfAcademicsException $exception) {
    handleAcademicsError($exception);
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('ENROLLMENT_EXISTS', 'This member is already enrolled in this batch.', 422);
    }
    throw $exception;
}
successResponse(getEnrollment($database, $enrollmentId), 'Enrollment created successfully.', 201);

