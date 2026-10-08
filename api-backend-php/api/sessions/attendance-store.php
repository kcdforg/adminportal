<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$session = getSession($database, $id);
if ($session === null) errorResponse('NOT_FOUND', 'Session not found.', 404);
requirePermission(canAccessAttendance($database, $jwt, $session, $session['batch']));
if ($session['attendance_locked']) errorResponse('ATTENDANCE_LOCKED', 'Attendance for this session is locked.', 403);
$body = getJsonBody();
$errors = [];
$statuses = ['present', 'absent', 'late', 'excused'];
if (empty($body['records']) || !is_array($body['records'])) {
    $errors['records'] = ['The records field is required and must be an array.'];
} else {
    foreach ($body['records'] as $index => $record) {
        if (!is_array($record) || empty($record['member_id']) || !is_numeric($record['member_id'])) $errors["records.{$index}.member_id"] = ['The member_id field is required and must be a valid integer.'];
        if (!is_array($record) || empty($record['attendance_status'])) $errors["records.{$index}.attendance_status"] = ['The attendance_status field is required.'];
        elseif (!in_array($record['attendance_status'], $statuses, true)) $errors["records.{$index}.attendance_status"] = ['The attendance_status must be one of: present, absent, late, excused.'];
    }
}
validateOrFail($errors);
$actorId = getActorId($jwt);
$markedAt = date('Y-m-d H:i:s');
try {
    $count = academicsTransaction($database, static function () use ($database, $jwt, $id, $body, $actorId, $markedAt): int {
        $lockedSession = lockSession($database, $id);
        if ($lockedSession === null) failAcademics('NOT_FOUND', 'Session not found.', 404);
        $batch = getBatch($database, (int) $lockedSession['batch_id'], false);
        if (!canAccessAttendance($database, $jwt, $lockedSession, $batch)) {
            failAcademics('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
        }
        if ((bool) $lockedSession['attendance_locked']) failAcademics('ATTENDANCE_LOCKED', 'Attendance for this session is locked.', 403);
        $saved = 0;
        foreach ($body['records'] as $record) {
            $memberId = (int) $record['member_id'];
            if (!recordExists($database, 'batch_members', ['batch_id' => (int) $lockedSession['batch_id'], 'member_id' => $memberId, 'status' => 'active'])) {
                failAcademics('VALIDATION_FAILED', "Member #{$memberId} is not enrolled in this batch.", 422, ['records.member_id' => ["Member #{$memberId} is not enrolled in this batch."]]);
            }
            $where = ['batch_session_id' => $id, 'member_id' => $memberId];
            $payload = [
                'attendance_status' => $record['attendance_status'],
                'remarks' => $record['remarks'] ?? null,
                'marked_by_member_id' => $actorId,
                'marked_at' => $markedAt,
            ];
            if ($database->has('attendance', $where)) {
                $database->update('attendance', $payload, $where);
            } else {
                $database->insert('attendance', $payload + $where);
            }
            $saved++;
        }
        logActivity($database, ['profile_id' => $actorId], 'bulk_attendance', 'batch_sessions', $id, null, ['records_saved' => $saved]);
        return $saved;
    });
} catch (KcdfAcademicsException $exception) {
    handleAcademicsError($exception);
}
successResponse(['records_saved' => $count], "{$count} attendance record(s) saved.");

