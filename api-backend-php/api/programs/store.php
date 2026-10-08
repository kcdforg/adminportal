<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
requirePermission(hasElevatedAccess($jwt));
$body = getJsonBody();
validateOrFail(validateProgram($body, 'create'));
$database->insert('programs', [
    'program_name' => $body['program_name'],
    'program_type' => $body['program_type'],
    'description' => $body['description'] ?? null,
    'age_group' => $body['age_group'] ?? null,
    'fee_amount' => (float) $body['fee_amount'],
    'status' => $body['status'] ?? 'active',
]);
$id = (int) $database->id();
$row = getProgram($database, $id);
logActivity($database, $jwt, 'create', 'programs', $id, null, $row);
successResponse($row, 'Program created successfully.', 201);

