<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

    requirePermission(isAdmin($jwt));
    $result = paginate(
        $database,
        'member_profiles',
        $_GET,
        ['status' => 'status'],
        ['id', 'first_name', 'last_name', 'created_at'],
        ['first_name', 'last_name', 'email', 'mobile']
    );

    jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

