<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

$old = getSession($database, $id);
if ($old === null) {
        errorResponse('NOT_FOUND', 'Session not found.', 404);
    }

requirePermission(canAccessSession($database, $jwt, $old, $old['batch'], 'edit'));
$body = getJsonBody();
validateOrFail(validateSession($body, 'update'));

$allowed = hasElevatedAccess($jwt)
    ? ['session_number', 'session_title', 'session_date', 'start_time', 'end_time', 'session_type', 'status', 'trainer_id', 'topics_covered', 'homework', 'notes']
    : ['topics_covered', 'homework', 'notes'];

$update = array_intersect_key($body, array_flip($allowed));
foreach (['session_number', 'trainer_id'] as $field) {
        if (array_key_exists($field, $update) && $update[$field] !== null) {
            $update[$field] = (int) $update[$field];
        }
    }

if ($update !== []) {
        $database->update('batch_sessions', $update, ['id' => $id]);
    }

$session = getSession($database, $id);
logActivity($database, $jwt, 'update', 'batch_sessions', $id, $old, $session);
successResponse($session, 'Session updated successfully.');

