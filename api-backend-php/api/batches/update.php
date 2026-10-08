<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
$old = getBatch($database, $id, false);
if ($old === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);
$body = getJsonBody();
validateOrFail(validateBatch($body, 'update'));
if (!empty($body['trainer_id']) && !recordExists($database, 'trainers', ['id' => (int) $body['trainer_id']])) errorResponse('NOT_FOUND', 'Trainer not found.', 404);
$update = array_intersect_key($body, array_flip(['batch_name', 'capacity', 'trainer_id', 'start_date', 'end_date', 'status']));
if (array_key_exists('capacity', $update) && $update['capacity'] !== null) $update['capacity'] = (int) $update['capacity'];
if (array_key_exists('trainer_id', $update)) $update['trainer_id'] = $update['trainer_id'] === null ? null : (int) $update['trainer_id'];
if ($update !== []) $database->update('student_batches', $update, ['id' => $id]);
$batch = getBatch($database, $id);
logActivity($database, $jwt, 'update', 'student_batches', $id, $old, $batch);
successResponse($batch, 'Batch updated successfully.');

