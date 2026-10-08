<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'family'));

$family = databaseTransaction($database, static function () use ($database, $body, $jwt): array {
        $addressId = !empty($body['address']) ? createAddress($database, $body['address']) : null;
        $database->insert('families', [
            'family_code' => 'KCDF-TEMP-' . bin2hex(random_bytes(8)),
            'family_name' => $body['family_name'],
            'address_id' => $addressId,
            'status' => 'active',
        ]);
        $id = (int) $database->id();
        $database->update('families', [
            'family_code' => 'KCDF-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT),
        ], ['id' => $id]);
        $family = getFamily($database, $id);
        logActivity($database, $jwt, 'create', 'families', $id, null, $family);
        return $family;
    });

successResponse($family, 'Family created successfully.', 201);

