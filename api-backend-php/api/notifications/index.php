<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$result = paginateCommunity($database, 'notifications', $_GET, [
    'member_id' => getUserId($jwt),
], ['status' => 'status', 'type' => 'type']);
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

