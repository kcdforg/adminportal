<?php

declare(strict_types=1);

require_once __DIR__ . '/identity.php';

final class KcdfCommunityException extends RuntimeException
{
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $status = 422,
        public readonly array $details = []
    ) {
        parent::__construct($message);
    }
}

function failCommunity(string $code, string $message, int $status = 422, array $details = []): never
{
    throw new KcdfCommunityException($code, $message, $status, $details);
}

function handleCommunityError(KcdfCommunityException $exception): never
{
    errorResponse($exception->errorCode, $exception->getMessage(), $exception->status, $exception->details);
}

function communityTransaction(Medoo\Medoo $database, callable $callback)
{
    $pdo = $database->pdo;
    $sqlite = $database->type === 'sqlite';
    if ($sqlite) {
        $pdo->exec('BEGIN IMMEDIATE');
    } else {
        $pdo->beginTransaction();
    }
    try {
        $result = $callback($pdo);
        if ($sqlite) {
            $pdo->exec('COMMIT');
        } else {
            $pdo->commit();
        }
        return $result;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            if ($sqlite) {
                $pdo->exec('ROLLBACK');
            } else {
                $pdo->rollBack();
            }
        }
        throw $exception;
    }
}

function isCommunityAdmin(array $jwt): bool
{
    return isAdmin($jwt);
}

function isParent(array $jwt): bool
{
    return count(array_intersect(getUserRoles($jwt), ['family_primary', 'family_normal'])) > 0;
}

function requireGroupAdmin(array $jwt, string $message = 'Only admins can perform this action.'): void
{
    if (!isCommunityAdmin($jwt)) {
        errorResponse('UNAUTHORIZED', $message, 403);
    }
}

function getGroupMember(Medoo\Medoo $database, int $groupId, int $memberId): ?array
{
    return findOne($database, 'group_members', ['group_id' => $groupId, 'member_id' => $memberId]);
}

function isActiveGroupMember(Medoo\Medoo $database, int $groupId, int $memberId): bool
{
    return $database->has('group_members', ['group_id' => $groupId, 'member_id' => $memberId, 'status' => 'active']);
}

function canViewGroup(Medoo\Medoo $database, array $jwt, array $group): bool
{
    if (isCommunityAdmin($jwt)) {
        return true;
    }

    if ($group['visibility'] === 'public') {
        return isParent($jwt);
    }

    return getUserId($jwt) > 0
        && isActiveGroupMember($database, (int) $group['id'], getUserId($jwt));
}

function canManageGroupMembers(Medoo\Medoo $database, array $jwt, array $group): bool
{
    if (isCommunityAdmin($jwt)) {
        return true;
    }

    return getUserId($jwt) > 0
        && isActiveGroupMember($database, (int) $group['id'], getUserId($jwt));
}

function canAccessGroup(Medoo\Medoo $database, array $jwt, array $group, string $scope): bool
{
    return match ($scope) {
        'view' => canViewGroup($database, $jwt, $group),
        'members' => canManageGroupMembers($database, $jwt, $group),
        default => canViewGroup($database, $jwt, $group),
    };
}

function getMemberNotification(Medoo\Medoo $database, int $notificationId, int $memberId): ?array
{
    return findOne($database, 'notifications', ['id' => $notificationId, 'member_id' => $memberId]);
}

function hasDuplicateInvitation(Medoo\Medoo $database, int $senderId, ?string $mobile, ?string $email): bool
{
    $or = [];
    if ($mobile !== null) $or['invite_mobile'] = $mobile;
    if ($email !== null) $or['invite_email'] = $email;
    if ($or === []) return false;
    return $database->has('invitations', [
        'invited_by_member_id' => $senderId,
        'status' => 'pending',
        'OR' => $or,
    ]);
}

function assertInvitationPending(array $invitation): void
{
    if (($invitation['status'] ?? null) !== 'pending') {
        failCommunity('INVITATION_NOT_PENDING', 'This invitation cannot be accepted.');
    }
}

function validateGroup(array $data, string $mode): array
{
    $errors = [];
    $visibility = ['public', 'private', 'invite_only'];
    $statuses = ['active', 'inactive', 'archived'];
    if ($mode === 'create') {
        validateRequired($errors, 'group_name', $data['group_name'] ?? null, 'The group_name field is required.');
        validateRequired($errors, 'visibility', $data['visibility'] ?? null, 'The visibility field is required.');
    }
    if (isset($data['group_name']) && strlen((string) $data['group_name']) > 255) {
        $errors['group_name'] = ['The group_name must not exceed 255 characters.'];
    }
    validateChoice($errors, 'visibility', $data['visibility'] ?? null, $visibility, 'The visibility must be one of: public, private, invite_only.');
    validateChoice($errors, 'status', $data['status'] ?? null, $statuses, 'The status must be one of: active, inactive, archived.');
    if ($mode === 'member-action') {
        validateRequired($errors, 'action', $data['action'] ?? null, 'The action field is required.');
        validateChoice($errors, 'action', $data['action'] ?? null, ['ban', 'remove'], 'The action must be one of: ban, remove.');
    }
    return $errors;
}

function validateInvitation(array $data, string $mode): array
{
    $errors = [];
    if ($mode === 'create') {
        $hasMobile = !empty($data['invite_mobile']);
        $hasEmail = !empty($data['invite_email']);
        if (!$hasMobile && !$hasEmail) {
            $errors['invite_mobile'] = ['At least one of invite_mobile or invite_email is required.'];
        }
        if ($hasMobile && !preg_match('/^\d{10,}$/', (string) $data['invite_mobile'])) {
            $errors['invite_mobile'] = ['The invite_mobile must contain digits only, minimum 10 digits.'];
        }
        if ($hasEmail && !filter_var($data['invite_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['invite_email'] = ['The invite_email must be a valid email address.'];
        }
        return $errors;
    }
    foreach (['first_name', 'last_name'] as $field) {
        if (empty($data[$field])) {
            $errors[$field] = ["The {$field} field is required."];
        } elseif (strlen((string) $data[$field]) > 100) {
            $errors[$field] = ["The {$field} must not exceed 100 characters."];
        }
    }
    validateRequired($errors, 'mobile', $data['mobile'] ?? null, 'The mobile field is required.');
    if (isset($data['mobile']) && $data['mobile'] !== '' && !preg_match('/^\d{10,}$/', (string) $data['mobile'])) {
        $errors['mobile'] = ['The mobile must contain digits only, minimum 10 digits.'];
    }
    validateRequired($errors, 'email', $data['email'] ?? null, 'The email field is required.');
    validateEmail($errors, 'email', $data['email'] ?? null, 'The email must be a valid email address.');
    validateRequired($errors, 'password', $data['password'] ?? null, 'The password field is required.');
    if (isset($data['password']) && $data['password'] !== '' && strlen((string) $data['password']) < 8) {
        $errors['password'] = ['The password must be at least 8 characters.'];
    }
    return $errors;
}

function generateInviteCode(Medoo\Medoo $database): string
{
    do {
        $code = strtoupper(bin2hex(random_bytes(16)));
    } while ($database->has('invitations', ['invite_code' => $code]));
    return $code;
}

function isInvitationExpired(string $sentAt, ?int $now = null): bool
{
    $sentTimestamp = strtotime($sentAt);
    return $sentTimestamp === false || ($now ?? time()) > $sentTimestamp + (7 * 24 * 3600);
}

function validateNotificationCommon(array $data): array
{
    $errors = [];
    $types = ['in_app', 'push', 'email'];
    if (empty($data['title'])) {
        $errors['title'] = ['The title field is required.'];
    } elseif (strlen((string) $data['title']) > 255) {
        $errors['title'] = ['The title must not exceed 255 characters.'];
    }
    if (empty($data['message'])) {
        $errors['message'] = ['The message field is required.'];
    } elseif (strlen((string) $data['message']) > 2000) {
        $errors['message'] = ['The message must not exceed 2000 characters.'];
    }
    if (empty($data['type'])) {
        $errors['type'] = ['The type field is required.'];
    } elseif (!in_array($data['type'], $types, true)) {
        $errors['type'] = ['The type must be one of: in_app, push, email.'];
    }
    return $errors;
}

function validateNotification(array $data, string $mode): array
{
    $errors = validateNotificationCommon($data);
    if ($mode === 'send') {
        if (!isset($data['member_ids']) || !is_array($data['member_ids']) || $data['member_ids'] === []) {
            $errors['member_ids'] = ['The member_ids field is required and must be a non-empty array.'];
        } else {
            foreach ($data['member_ids'] as $id) {
                if (!is_numeric($id) || (int) $id <= 0) {
                    $errors['member_ids'] = ['All member_ids must be valid positive integers.'];
                    break;
                }
            }
        }
    } else {
        $types = ['batch', 'group', 'all_families'];
        if (empty($data['target_type'])) {
            $errors['target_type'] = ['The target_type field is required.'];
        } elseif (!in_array($data['target_type'], $types, true)) {
            $errors['target_type'] = ['The target_type must be one of: batch, group, all_families.'];
        }
        if (in_array($data['target_type'] ?? '', ['batch', 'group'], true) && empty($data['target_id'])) {
            $errors['target_id'] = ['The target_id is required when target_type is batch or group.'];
        } elseif (isset($data['target_id']) && $data['target_id'] !== '' && (!is_numeric($data['target_id']) || (int) $data['target_id'] < 1)) {
            $errors['target_id'] = ['The target_id must be a valid positive integer.'];
        }
    }
    return $errors;
}

function paginateCommunity(Medoo\Medoo $database, string $table, array $filters, array $where = [], array $filterMap = [], string $sortField = 'created_at', string $order = 'DESC', int $maxPerPage = 100): array
{
    foreach ($filterMap as $input => $column) {
        if (isset($filters[$input]) && $filters[$input] !== '') {
            $where[$column] = $filters[$input];
        }
    }
    $total = (int) $database->count($table, $where);
    $perPage = min(max((int) ($filters['per_page'] ?? 20), 1), $maxPerPage);
    $page = max((int) ($filters['page'] ?? 1), 1);
    $rows = $database->select($table, '*', array_merge($where, [
        'ORDER' => [$sortField => $order],
        'LIMIT' => [($page - 1) * $perPage, $perPage],
    ])) ?? [];
    return [
        'data' => array_map('castIds', $rows),
        'meta' => ['total' => $total, 'per_page' => $perPage, 'current_page' => $page, 'last_page' => max(1, (int) ceil($total / $perPage))],
    ];
}

function lockInvitation(Medoo\Medoo $database, string $code): ?array
{
    if ($database->type === 'sqlite') {
        return findOne($database, 'invitations', ['invite_code' => $code]);
    }
    $statement = $database->pdo->prepare('SELECT * FROM invitations WHERE invite_code = :code FOR UPDATE');
    $statement->execute([':code' => $code]);
    $row = $statement->fetch(PDO::FETCH_ASSOC);
    return is_array($row) ? castIds($row) : null;
}

function issueInvitationTokens(Medoo\Medoo $database, array $bootstrap, int $profileId, string $email): array
{
    $login = $database->get('user_logins', [
        'id',
        'profile_id',
        'username',
        'user_type',
        'role',
        'is_active',
    ], [
        'profile_id' => $profileId,
        'username' => $email,
        'user_type' => 'member',
        'is_active' => 1,
    ]);
    $profile = getProfileById($database, $profileId);
    if (!is_array($login) || $profile === null) {
        throw new RuntimeException('The new member login could not be retrieved.');
    }
    $tokens = issueLoginTokens($database, $bootstrap, castIds($login), $profile);
    return [
        'access_token' => $tokens['access_token'],
        'refresh_token' => $tokens['refresh_token'],
    ];
}
