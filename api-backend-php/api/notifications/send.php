<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isElevatedAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'Only admins can send notifications.', 403);
}
$body = getJsonBody();
validateOrFail(validateNotification($body, 'send'));
$memberIds = array_map('intval', $body['member_ids']);
$count = count($memberIds);
communityTransaction($database, static function () use ($database, $jwt, $body, $memberIds, $count): void {
    $now = date('Y-m-d H:i:s');
    foreach ($memberIds as $memberId) {
        $database->insert('notifications', [
            'member_id' => $memberId,
            'title' => $body['title'],
            'message' => $body['message'],
            'type' => $body['type'],
            'status' => 'unread',
            'created_at' => $now,
        ]);
    }
    logActivity($database, $jwt, 'notification_sent', 'notifications', 0, null, [
        'count' => $count,
        'title' => $body['title'],
        'type' => $body['type'],
    ]);
});
successResponse(['sent_to' => $count], 'Notifications sent successfully.');

