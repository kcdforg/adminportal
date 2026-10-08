<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requirePermission(isAdmin($jwt));
$result = paginate(
    $database,
    'families',
    $_GET,
    ['status' => 'status'],
    ['id', 'family_name', 'family_code', 'created_at'],
    ['family_name']
);

foreach ($result['data'] as &$family) {
    $family['address'] = getAddress($database, $family['address_id']);
}
unset($family);

jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

