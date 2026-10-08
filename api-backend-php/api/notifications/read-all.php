<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$database->update('notifications', [
    'status' => 'read',
    'read_at' => date('Y-m-d H:i:s'),
], ['member_id' => getUserId($jwt), 'status' => 'unread']);
$count = (int) $database->rowCount();
successResponse(['updated' => $count], 'All notifications marked as read.');

