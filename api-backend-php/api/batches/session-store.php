<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$batchId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
$batch = getBatch($database, $batchId, false);
if ($batch === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);
$body = getJsonBody();
validateOrFail(validateSession($body, 'create'));
$trainerId = array_key_exists('trainer_id', $body) && $body['trainer_id'] !== null
? (int) $body['trainer_id'] : ($batch['trainer_id'] === null ? null : (int) $batch['trainer_id']);
$database->insert('batch_sessions', [
    'batch_id' => $batchId,
    'session_number' => isset($body['session_number']) ? (int) $body['session_number'] : null,
    'session_title' => $body['session_title'] ?? null,
    'session_date' => $body['session_date'],
    'start_time' => $body['start_time'] ?? null,
    'end_time' => $body['end_time'] ?? null,
    'session_type' => $body['session_type'],
    'status' => $body['status'] ?? 'scheduled',
    'trainer_id' => $trainerId,
]);
$id = (int) $database->id();
$session = getSession($database, $id);
logActivity($database, $jwt, 'create', 'batch_sessions', $id, null, $session);
successResponse($session, 'Session created successfully.', 201);

