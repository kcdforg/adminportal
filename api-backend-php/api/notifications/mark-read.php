<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$where = ['id' => $id, 'member_id' => getUserId($jwt)];
$notification = getMemberNotification($database, $id, getUserId($jwt));
if ($notification === null) {
    errorResponse('NOT_FOUND', 'Notification not found.', 404);
}
if ($notification['status'] !== 'read') {
    $database->update('notifications', ['status' => 'read', 'read_at' => date('Y-m-d H:i:s')], $where);
}
successResponse(getMemberNotification($database, $id, getUserId($jwt)), 'Notification marked as read.');

