<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isElevatedAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'Only admins can broadcast notifications.', 403);
}
$body = getJsonBody();
validateOrFail(validateNotification($body, 'broadcast'));
$memberIds = match ($body['target_type']) {
    'batch' => $database->select('batch_members', 'member_id', [
        'batch_id' => (int) $body['target_id'],
        'status' => 'active',
    ]) ?? [],
    'group' => $database->select('group_members', 'member_id', [
        'group_id' => (int) $body['target_id'],
        'status' => 'active',
    ]) ?? [],
    'all_families' => $database->select('family_members', 'profile_id', ['status' => 'active']) ?? [],
    default => [],
};
$memberIds = array_values(array_unique(array_map('intval', $memberIds)));
$count = count($memberIds);
if ($count > 0) {
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
        logActivity($database, $jwt, 'notification_broadcast', 'notifications', 0, null, [
            'target_type' => $body['target_type'],
            'target_id' => !empty($body['target_id']) ? (int) $body['target_id'] : null,
            'title' => $body['title'],
            'type' => $body['type'],
            'count' => $count,
        ]);
    });
}
successResponse(['sent_to' => $count], 'Notification broadcast successfully.');

