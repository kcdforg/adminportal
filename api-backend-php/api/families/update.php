<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$id = (int) ($routeParams['id'] ?? 0);

$family = getFamily($database, $id);
if ($family === null) {
        errorResponse('NOT_FOUND', 'Family not found.', 404);
    }

requirePermission(canAccessFamily($database, $jwt, $id, 'edit'));
$body = getJsonBody();
validateOrFail(validateIdentityFields($body, 'family', true));

$updated = databaseTransaction($database, static function () use ($database, $body, $family, $jwt, $id): array {
        $addressId = $family['address_id'];
        if (!empty($body['address'])) {
            if ($addressId) {
                updateAddress($database, (int) $addressId, $body['address']);
            } else {
                $addressId = createAddress($database, $body['address']);
                $database->update('families', ['address_id' => $addressId], ['id' => $id]);
            }
        }

        $update = [];
        foreach (['family_name', 'status'] as $field) {
            if (array_key_exists($field, $body)) {
                $update[$field] = $body[$field];
            }
        }

        if ($update !== []) {
            $database->update('families', $update, ['id' => $id]);
        }

        $row = getFamily($database, $id);
        logActivity($database, $jwt, 'update', 'families', $id, $family, $row);
        return $row;
    });

successResponse($updated, 'Family updated successfully.');

