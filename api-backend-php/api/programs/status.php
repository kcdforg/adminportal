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
validateOrFail(validateProgram($body, 'status'));
$database->update('programs', ['status' => $body['status']], ['id' => $id]);
$row = getProgram($database, $id);
logActivity($database, $jwt, 'status_change', 'programs', $id, $old, $row);
successResponse($row, 'Program status updated successfully.');

