<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$old = getEnrollment($database, $id);
if ($old === null) errorResponse('NOT_FOUND', 'Enrollment not found.', 404);
requirePermission(canAccessEnrollment($database, $jwt, $old, 'cancel'));
try {
    academicsTransaction($database, static function () use ($database, $jwt, $id): void {
        $enrollment = lockEnrollment($database, $id);
        if ($enrollment === null) failAcademics('NOT_FOUND', 'Enrollment not found.', 404);
        if (!canAccessEnrollment($database, $jwt, $enrollment, 'cancel')) {
            failAcademics('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
        }
        if ($enrollment['status'] === 'cancelled') failAcademics('ALREADY_CANCELLED', 'Enrollment is already cancelled.');
        $oldValues = getEnrollment($database, $id);
        $database->update('enrollments', ['status' => 'cancelled'], ['id' => $id]);
        $database->update('batch_members', ['status' => 'dropped'], [
            'batch_id' => (int) $enrollment['batch_id'],
            'member_id' => (int) $enrollment['member_id'],
        ]);
        $updated = getEnrollment($database, $id);
        logActivity($database, ['profile_id' => null], 'cancel', 'enrollments', $id, $oldValues, $updated);
    });
} catch (KcdfAcademicsException $exception) {
    handleAcademicsError($exception);
}
successResponse(getEnrollment($database, $id), 'Enrollment cancelled successfully.');

