<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'family'));
if (isset($body['family_code']) && is_string($body['family_code'])
    && recordExists($database, 'families', ['family_code' => trim($body['family_code'])])) {
    validateOrFail(['family_code' => ['The family_code has already been taken.']]);
}

$database->insert('families', [
    'family_code' => trim($body['family_code']),
    'family_name' => trim($body['family_name']),
    'address_id' => null,
    'status' => 'active',
]);
$id = (int) $database->id();
$family = getFamily($database, $id);
logActivity($database, $jwt, 'create', 'families', $id, null, $family);

successResponse($family, 'Family created successfully.', 201);
