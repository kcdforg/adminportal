<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/community-notifications.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$invitation = findOne($database, 'invitations', ['id' => $id]);
if ($invitation === null) {
    errorResponse('NOT_FOUND', 'Invitation not found.', 404);
}
if (!isCommunityAdmin($jwt) && getUserId($jwt) !== (int) $invitation['invited_by_member_id']) {
    errorResponse('UNAUTHORIZED', 'You do not have permission to cancel this invitation.', 403);
}
try {
    communityTransaction($database, static function () use ($database, $jwt, $id): void {
        $pdo = $database->pdo;
        $statement = $pdo->prepare('SELECT * FROM invitations WHERE id = :id' . ($database->type === 'sqlite' ? '' : ' FOR UPDATE'));
        $statement->execute([':id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!is_array($row)) failCommunity('NOT_FOUND', 'Invitation not found.', 404);
        if (!isCommunityAdmin($jwt) && getUserId($jwt) !== (int) $row['invited_by_member_id']) {
            failCommunity('UNAUTHORIZED', 'You do not have permission to cancel this invitation.', 403);
        }
        if ($row['status'] !== 'pending') {
            failCommunity('INVITATION_NOT_PENDING', 'Only pending invitations can be cancelled.');
        }
        $database->update('invitations', ['status' => 'cancelled'], ['id' => $id, 'status' => 'pending']);
        logActivity($database, $jwt, 'status_changed', 'invitations', $id, ['status' => 'pending'], ['status' => 'cancelled']);
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
}
successResponse(null, 'Invitation cancelled successfully.');

