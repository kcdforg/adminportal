<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!isParent($jwt) && !isCommunityAdmin($jwt)) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to send invitations.', 403);
}
$body = getJsonBody();
validateOrFail(validateInvitation($body, 'create'));
$profileId = getUserId($jwt);
if (hasDuplicateInvitation(
    $database,
    $profileId,
    !empty($body['invite_mobile']) ? (string) $body['invite_mobile'] : null,
    !empty($body['invite_email']) ? (string) $body['invite_email'] : null
)) {
    errorResponse('DUPLICATE_INVITATION', 'An active invitation has already been sent to this contact.', 409);
}
$code = generateInviteCode($database);
try {
    $id = communityTransaction($database, static function () use ($database, $jwt, $body, $profileId, $code): int {
        $database->insert('invitations', [
            'invited_by_member_id' => $profileId,
            'invite_mobile' => !empty($body['invite_mobile']) ? $body['invite_mobile'] : null,
            'invite_email' => !empty($body['invite_email']) ? $body['invite_email'] : null,
            'invite_code' => $code,
            'status' => 'pending',
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
        $id = (int) $database->id();
        logActivity($database, $jwt, 'created', 'invitations', $id, null, ['status' => 'pending']);
        return $id;
    });
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('DUPLICATE_INVITATION', 'An active invitation has already been sent to this contact.', 409);
    }
    throw $exception;
}
successResponse(findOne($database, 'invitations', ['id' => $id]), 'Invitation sent successfully.', 201);

