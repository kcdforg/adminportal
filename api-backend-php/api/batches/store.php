<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
$body = getJsonBody();
validateOrFail(validateBatch($body, 'create'));
$programId = (int) $body['program_id'];
if (!recordExists($database, 'programs', ['id' => $programId])) errorResponse('NOT_FOUND', 'Program not found.', 404);
if (!empty($body['trainer_id']) && !recordExists($database, 'trainers', ['id' => (int) $body['trainer_id']])) errorResponse('NOT_FOUND', 'Trainer not found.', 404);
$database->insert('student_batches', [
    'program_id' => $programId,
    'batch_name' => $body['batch_name'],
    'capacity' => isset($body['capacity']) ? (int) $body['capacity'] : null,
    'trainer_id' => !empty($body['trainer_id']) ? (int) $body['trainer_id'] : null,
    'start_date' => $body['start_date'] ?? null,
    'end_date' => $body['end_date'] ?? null,
    'status' => $body['status'] ?? 'upcoming',
]);
$id = (int) $database->id();
$batch = getBatch($database, $id);
logActivity($database, $jwt, 'create', 'student_batches', $id, null, $batch);
successResponse($batch, 'Batch created successfully.', 201);

