<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
$old = getProgram($database, $id);
if ($old === null) errorResponse('NOT_FOUND', 'Program not found.', 404);
$body = getJsonBody();
validateOrFail(validateProgram($body, 'update'));
$allowed = ['program_name', 'program_type', 'description', 'age_group', 'fee_amount', 'status'];
$update = array_intersect_key($body, array_flip($allowed));
if (array_key_exists('fee_amount', $update)) $update['fee_amount'] = (float) $update['fee_amount'];
if ($update !== []) $database->update('programs', $update, ['id' => $id]);
$row = getProgram($database, $id);
logActivity($database, $jwt, 'update', 'programs', $id, $old, $row);
successResponse($row, 'Program updated successfully.');

