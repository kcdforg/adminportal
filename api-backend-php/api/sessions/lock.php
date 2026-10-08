<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
try {
    academicsTransaction($database, static function () use ($database, $jwt, $id): void {
        $raw = lockSession($database, $id);
        if ($raw === null) failAcademics('NOT_FOUND', 'Session not found.', 404);
        if ((bool) $raw['attendance_locked']) failAcademics('ALREADY_LOCKED', 'Session attendance is already locked.');
        $old = getSession($database, $id);
        $database->update('batch_sessions', ['attendance_locked' => 1], ['id' => $id]);
        $session = getSession($database, $id);
        logActivity($database, $jwt, 'lock', 'batch_sessions', $id, $old, $session);
    });
} catch (KcdfAcademicsException $exception) {
    handleAcademicsError($exception);
}
$session = getSession($database, $id);
successResponse($session, 'Session attendance locked.');

