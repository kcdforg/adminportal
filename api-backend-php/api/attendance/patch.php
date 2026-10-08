<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$old = findOne($database, 'attendance', ['id' => $id]);
if ($old === null) errorResponse('NOT_FOUND', 'Attendance record not found.', 404);
$session = getSession($database, (int) $old['batch_session_id']);
if ($session === null || !canAccessAttendance($database, $jwt, $session, $session['batch'])) errorResponse('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
if ($session['attendance_locked']) errorResponse('ATTENDANCE_LOCKED', 'Attendance for this session is locked.', 403);
$body = getJsonBody();
$statuses = ['present', 'absent', 'late', 'excused'];
$errors = [];
if (!array_key_exists('attendance_status', $body)) $errors['attendance_status'] = ['The attendance_status field is required.'];
elseif (!in_array($body['attendance_status'], $statuses, true)) $errors['attendance_status'] = ['The attendance_status must be one of: present, absent, late, excused.'];
validateOrFail($errors);
$update = array_intersect_key($body, array_flip(['attendance_status', 'remarks']));
$update['marked_by_member_id'] = getActorId($jwt);
$update['marked_at'] = date('Y-m-d H:i:s');
try {
    academicsTransaction($database, static function () use ($database, $jwt, $id, $old, $update): void {
        $lockedSession = lockSession($database, (int) $old['batch_session_id']);
        if ($lockedSession === null) failAcademics('NOT_FOUND', 'Session not found.', 404);
        $batch = getBatch($database, (int) $lockedSession['batch_id'], false);
        if (!canAccessAttendance($database, $jwt, $lockedSession, $batch)) {
            failAcademics('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
        }
        if ((bool) $lockedSession['attendance_locked']) failAcademics('ATTENDANCE_LOCKED', 'Attendance for this session is locked.', 403);
        $database->update('attendance', $update, ['id' => $id]);
        $row = findOne($database, 'attendance', ['id' => $id]);
        logActivity($database, $jwt, 'update', 'attendance', $id, $old, $row);
    });
} catch (KcdfAcademicsException $exception) {
    handleAcademicsError($exception);
}
$row = findOne($database, 'attendance', ['id' => $id]);
successResponse($row, 'Attendance record updated.');

