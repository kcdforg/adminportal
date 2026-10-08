<?php

declare(strict_types=1);

$database = $bootstrap['database'];

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$code = (string) ($routeParams['code'] ?? '');
if (!preg_match('/^[A-Za-z0-9]{12,50}$/', $code)) {
    errorResponse('NOT_FOUND', 'Invitation not found.', 404);
}
$database = $bootstrap['database'] ?? null;
if (!$database instanceof Medoo\Medoo) {
    errorResponse('STARTUP_ERROR', 'The API runtime has not been initialized.', 500);
}
$invitation = findOne($database, 'invitations', ['invite_code' => $code]);
if ($invitation === null) {
    errorResponse('NOT_FOUND', 'Invitation not found.', 404);
}
if ($invitation['status'] === 'accepted') {
    errorResponse('INVITATION_ALREADY_ACCEPTED', 'This invitation has already been accepted.', 422);
}
if ($invitation['status'] === 'cancelled') {
    errorResponse('INVITATION_CANCELLED', 'This invitation has been cancelled.', 422);
}
if ($invitation['status'] === 'expired' || isInvitationExpired((string) $invitation['sent_at'])) {
    if ($invitation['status'] !== 'expired') {
        $database->update('invitations', ['status' => 'expired'], ['id' => $invitation['id'], 'status' => 'pending']);
    }
    errorResponse('INVALID_INVITE_CODE', 'This invitation has expired.', 422);
}
$inviter = getProfile($database, (int) $invitation['invited_by_member_id']);
$name = $inviter === null ? 'Unknown' : trim($inviter['first_name'] . ' ' . $inviter['last_name']);
$expiresAt = gmdate('Y-m-d\TH:i:s\Z', strtotime((string) $invitation['sent_at']) + (7 * 24 * 3600));
successResponse([
    'invite_code' => $invitation['invite_code'],
    'invited_by' => $name,
    'status' => $invitation['status'],
    'expires_at' => $expiresAt,
]);

