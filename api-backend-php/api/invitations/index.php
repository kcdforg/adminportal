<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
$where = [];
if (!isCommunityAdmin($jwt)) {
    $where['invited_by_member_id'] = getUserId($jwt);
}
$result = paginateCommunity($database, 'invitations', $_GET, $where, ['status' => 'status'], 'sent_at');
jsonResponse(['success' => true, 'data' => $result['data'], 'meta' => $result['meta']]);

