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
$current = findOne($database, 'invitations', ['invite_code' => $code]);
if ($current === null) {
    errorResponse('NOT_FOUND', 'Invitation not found.', 404);
}
if (($current['status'] ?? null) !== 'pending') {
    errorResponse('INVITATION_NOT_PENDING', 'This invitation cannot be accepted.', 422);
}
if (isInvitationExpired((string) $current['sent_at'])) {
    $database->update('invitations', ['status' => 'expired'], ['id' => $current['id'], 'status' => 'pending']);
    errorResponse('INVALID_INVITE_CODE', 'This invitation has expired.', 422);
}
$body = getJsonBody();
validateOrFail(validateInvitation($body, 'accept'));
$email = (string) $body['email'];
$now = date('Y-m-d H:i:s');
try {
    $result = communityTransaction($database, static function (PDO $pdo) use ($database, $bootstrap, $code, $body, $email, $now): array {
        $invitation = lockInvitation($database, $code);
        if ($invitation === null) {
            failCommunity('NOT_FOUND', 'Invitation not found.', 404);
        }
        assertInvitationPending($invitation);
        if (isInvitationExpired((string) $invitation['sent_at'])) {
            $database->update('invitations', ['status' => 'expired'], ['id' => $invitation['id'], 'status' => 'pending']);
            return ['expired' => true];
        }
        $usernameQuery = $pdo->prepare('SELECT id FROM user_logins WHERE username = :username');
        $usernameQuery->execute([':username' => $email]);
        if ($usernameQuery->fetchColumn() !== false) {
            failCommunity('ACCOUNT_EXISTS', 'An account with this email already exists.', 409);
        }
        $insertProfile = $pdo->prepare(
            'INSERT INTO member_profiles (first_name, last_name, mobile, email, status) ' .
            "VALUES (:first_name, :last_name, :mobile, :email, 'active')"
        );
        $insertProfile->execute([
            ':first_name' => (string) $body['first_name'],
            ':last_name' => (string) $body['last_name'],
            ':mobile' => (string) $body['mobile'],
            ':email' => $email,
        ]);
        $profileId = (int) $pdo->lastInsertId();
        $insertLogin = $pdo->prepare(
            'INSERT INTO user_logins (profile_id, username, password_hash, user_type, role, is_active) ' .
            "VALUES (:profile_id, :username, :password_hash, 'member', NULL, 1)"
        );
        $insertLogin->execute([
            ':profile_id' => $profileId,
            ':username' => $email,
            ':password_hash' => password_hash((string) $body['password'], PASSWORD_BCRYPT),
        ]);
        $updateInvitation = $pdo->prepare(
            "UPDATE invitations SET status = 'accepted', accepted_at = :accepted_at WHERE id = :id AND status = 'pending'"
        );
        $updateInvitation->execute([':accepted_at' => $now, ':id' => $invitation['id']]);
        if ($updateInvitation->rowCount() !== 1) {
            failCommunity('INVITATION_NOT_PENDING', 'This invitation cannot be accepted.');
        }
        logActivity($database, ['profile_id' => $profileId], 'invitation_accepted', 'invitations', (int) $invitation['id'], ['status' => 'pending'], [
            'status' => 'accepted',
            'new_profile_id' => $profileId,
        ]);
        $tokens = issueInvitationTokens($database, $bootstrap, $profileId, $email);
        return ['expired' => false, 'tokens' => $tokens];
    });
} catch (KcdfCommunityException $exception) {
    handleCommunityError($exception);
} catch (PDOException $exception) {
    if (in_array((string) $exception->getCode(), ['23000', '19'], true)) {
        errorResponse('ACCOUNT_EXISTS', 'An account with this email already exists.', 409);
    }
    throw $exception;
}
if ($result['expired']) {
    errorResponse('INVALID_INVITE_CODE', 'This invitation has expired.', 422);
}
successResponse($result['tokens'], 'Invitation accepted. Account created successfully.');
