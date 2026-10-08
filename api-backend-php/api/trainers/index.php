<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

    requirePermission(isAdmin($jwt));
    $result = paginate($database, 'trainers', $_GET, ['status' => 'status'], ['id', 'trainer_code', 'created_at']);

    foreach ($result['data'] as &$trainer) {
        $trainer['profile'] = getProfile($database, (int) $trainer['profile_id']);
        $trainer['address'] = getAddress($database, $trainer['address_id']);
        if ($trainer['joined_at'] !== null) {
            $trainer['joined_at'] .= ' 00:00:00';
        }
    }
    unset($trainer);

    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

