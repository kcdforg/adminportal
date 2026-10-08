<?php

declare(strict_types=1);

require __DIR__ . '/../config/autoload.php';
require __DIR__ . '/../services/community-notifications.php';

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "phase-6 checks require PDO_SQLITE\n");
    exit(2);
}

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
$database = new Medoo\Medoo(['pdo' => $pdo, 'type' => 'sqlite', 'database' => ':memory:']);
$pdo->exec('CREATE TABLE parent_groups (id INTEGER PRIMARY KEY, group_name TEXT NOT NULL, description TEXT NULL, visibility TEXT NOT NULL, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE group_members (id INTEGER PRIMARY KEY, group_id INTEGER NOT NULL, member_id INTEGER NOT NULL, joined_at TEXT NOT NULL, status TEXT NOT NULL, UNIQUE (group_id, member_id))');
$pdo->exec('CREATE TABLE invitations (id INTEGER PRIMARY KEY, invited_by_member_id INTEGER NOT NULL, invite_mobile TEXT NULL, invite_email TEXT NULL, invite_code TEXT NOT NULL UNIQUE, status TEXT NOT NULL, sent_at TEXT NOT NULL, accepted_at TEXT NULL)');
$pdo->exec('CREATE TABLE notifications (id INTEGER PRIMARY KEY, member_id INTEGER NOT NULL, title TEXT NOT NULL, message TEXT NOT NULL, type TEXT NOT NULL, status TEXT NOT NULL, read_at TEXT NULL)');
$pdo->exec('CREATE TABLE member_profiles (id INTEGER PRIMARY KEY AUTOINCREMENT, first_name TEXT NOT NULL, last_name TEXT NOT NULL, mobile TEXT NOT NULL, email TEXT NOT NULL, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE user_logins (id INTEGER PRIMARY KEY AUTOINCREMENT, profile_id INTEGER NOT NULL, username TEXT NOT NULL UNIQUE, password_hash TEXT NOT NULL, is_active INTEGER NOT NULL)');
$pdo->exec('CREATE TABLE activity_logs (id INTEGER PRIMARY KEY, actor_profile_id INTEGER NULL, action TEXT NOT NULL, entity_type TEXT NOT NULL, entity_id INTEGER NOT NULL, old_values TEXT NULL, new_values TEXT NULL)');
$pdo->exec("INSERT INTO parent_groups VALUES (1, 'Public', NULL, 'public', 'active'), (2, 'Private', NULL, 'private', 'active'), (3, 'Invite only', NULL, 'invite_only', 'active')");
$pdo->exec("INSERT INTO group_members VALUES (1, 2, 10, '2026-01-01 00:00:00', 'active'), (2, 3, 11, '2026-01-01 00:00:00', 'left'), (3, 2, 12, '2026-01-01 00:00:00', 'banned')");
$pdo->exec("INSERT INTO invitations VALUES (1, 10, '1234567890', 'invite@example.test', '0123456789ABCDEF', 'pending', '2026-10-01 12:00:00', NULL), (2, 10, NULL, 'expired@example.test', 'FEDCBA9876543210', 'expired', '2026-01-01 12:00:00', NULL)");
$pdo->exec("INSERT INTO notifications VALUES (1, 10, 'Own', 'Message', 'in_app', 'unread', NULL), (2, 11, 'Other', 'Message', 'email', 'unread', NULL)");

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};

$primary = ['profile_id' => 10, 'roles' => ['family_primary']];
$normal = ['profile_id' => 11, 'roles' => ['family_normal']];
$admin = ['profile_id' => 99, 'roles' => ['admin_super']];
$assert(kcdf_community_group_access($database, $primary, ['id' => 1, 'visibility' => 'public'], 'view'), 'Parents must view public groups.');
$assert(kcdf_community_group_access($database, $primary, ['id' => 2, 'visibility' => 'private'], 'view'), 'Active group members must view private groups.');
$assert(!kcdf_community_group_access($database, $normal, ['id' => 2, 'visibility' => 'private'], 'view'), 'Non-members must not view private groups.');
$assert(!kcdf_community_group_access($database, ['profile_id' => 11, 'roles' => ['family_primary']], ['id' => 3, 'visibility' => 'invite_only'], 'members'), 'Former members must not view invite-only group membership.');
$assert(kcdf_community_group_access($database, $admin, ['id' => 3, 'visibility' => 'invite_only'], 'members'), 'Admins must have group access.');
$assert(!kcdf_community_group_access($database, ['profile_id' => 12, 'roles' => ['family_primary']], ['id' => 2, 'visibility' => 'private'], 'view'), 'Banned members must not have active group access.');

$assert(kcdf_community_invitation_validation(['invite_email' => 'bad'], 'create') !== [], 'Invalid invitation email must fail validation.');
$assert(kcdf_community_invitation_validation(['invite_mobile' => '1234567890'], 'create') === [], 'A valid mobile-only invitation must pass.');
$assert(kcdf_community_invitation_duplicate($database, 10, null, 'invite@example.test'), 'Pending invitations to the same contact must be detected.');
$assert(!kcdf_community_invitation_duplicate($database, 11, null, 'invite@example.test'), 'Duplicate checks must be scoped to the sender.');
$generatedCode = kcdf_community_generate_invite_code($database);
$assert((bool) preg_match('/^[A-F0-9]{32}$/', $generatedCode), 'Invitation codes must be high-entropy URL-safe hex.');
$assert(!$database->has('invitations', ['invite_code' => $generatedCode]), 'Generated invitation codes must be unique.');
$recent = date('Y-m-d H:i:s', time() - 60);
$assert(!kcdf_community_invitation_expired($recent), 'Recent invitations must be valid.');
$assert(kcdf_community_invitation_expired('2000-01-01 00:00:00'), 'Invitations older than seven days must expire.');
try {
    $accepted = kcdf_identity_one($database, 'invitations', ['id' => 1]);
    $accepted['status'] = 'accepted';
    kcdf_community_assert_invitation_pending($accepted);
    $assert(false, 'Accepted invitations must not be accepted twice.');
} catch (KcdfCommunityException $exception) {
    $assert($exception->errorCode === 'INVITATION_NOT_PENDING', 'Repeated acceptance must have the source business-rule code.');
}

$assert(kcdf_community_notification_for_member($database, 1, 10) !== null, 'A member must see their own notification.');
$assert(kcdf_community_notification_for_member($database, 2, 10) === null, 'A member must not see another member’s notification.');
$assert(kcdf_community_notification_validation([
    'member_ids' => [10, 11], 'title' => 'Title', 'message' => 'Message', 'type' => 'in_app',
], 'send') === [], 'Valid targeted notification data must pass validation.');
$assert(kcdf_community_notification_validation([
    'target_type' => 'invalid', 'title' => 'Title', 'message' => 'Message', 'type' => 'in_app',
], 'broadcast') !== [], 'Invalid broadcast targets must fail validation.');
$assert(kcdf_identity_is_elevated_admin(['roles' => ['admin_program_manager']]), 'Program managers must be eligible to send and broadcast notifications.');
$assert(!kcdf_identity_is_elevated_admin(['roles' => ['admin_accounts']]), 'Accounts admins must not bypass the elevated-admin notification restriction.');

$beforeGroups = (int) $pdo->query('SELECT COUNT(*) FROM parent_groups')->fetchColumn();
$beforeLogs = (int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn();
try {
    kcdf_community_transaction($database, static function () use ($database): void {
        $database->insert('parent_groups', ['group_name' => 'Rollback', 'visibility' => 'public', 'status' => 'active']);
        kcdf_identity_log($database, [], 'created', 'parent_groups', (int) $database->id(), null, ['group_name' => 'Rollback']);
        throw new RuntimeException('force rollback');
    });
    $assert(false, 'The rollback fixture should throw.');
} catch (RuntimeException $exception) {
    $assert($exception->getMessage() === 'force rollback', 'Transactions must preserve the original error.');
}
$assert((int) $pdo->query('SELECT COUNT(*) FROM parent_groups')->fetchColumn() === $beforeGroups, 'Group writes must roll back when audit logging fails.');
$assert((int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn() === $beforeLogs, 'Related audit writes must roll back with the operation.');

$beforeProfiles = (int) $pdo->query('SELECT COUNT(*) FROM member_profiles')->fetchColumn();
$beforeLogins = (int) $pdo->query('SELECT COUNT(*) FROM user_logins')->fetchColumn();
$invitationStatus = (string) $pdo->query('SELECT status FROM invitations WHERE id = 1')->fetchColumn();
try {
    kcdf_community_transaction($database, static function () use ($database): void {
        $database->insert('member_profiles', [
            'first_name' => 'New', 'last_name' => 'Member', 'mobile' => '1234567890',
            'email' => 'new@example.test', 'status' => 'active',
        ]);
        $profileId = (int) $database->id();
        $database->insert('user_logins', [
            'profile_id' => $profileId, 'username' => 'new@example.test',
            'password_hash' => password_hash('not-a-real-password', PASSWORD_BCRYPT), 'is_active' => 1,
        ]);
        $database->update('invitations', ['status' => 'accepted', 'accepted_at' => date('Y-m-d H:i:s')], ['id' => 1]);
        kcdf_identity_log($database, ['profile_id' => $profileId], 'invitation_accepted', 'invitations', 1, ['status' => 'pending'], ['status' => 'accepted']);
        throw new RuntimeException('force invitation rollback');
    });
    $assert(false, 'The invitation rollback fixture should throw.');
} catch (RuntimeException $exception) {
    $assert($exception->getMessage() === 'force invitation rollback', 'Invitation acceptance must rethrow failures.');
}
$assert((int) $pdo->query('SELECT COUNT(*) FROM member_profiles')->fetchColumn() === $beforeProfiles, 'Failed acceptance must roll back the new profile.');
$assert((int) $pdo->query('SELECT COUNT(*) FROM user_logins')->fetchColumn() === $beforeLogins, 'Failed acceptance must roll back the new login.');
$assert((string) $pdo->query('SELECT status FROM invitations WHERE id = 1')->fetchColumn() === $invitationStatus, 'Failed acceptance must leave the invitation pending.');
$assert((int) $pdo->query('SELECT COUNT(*) FROM activity_logs')->fetchColumn() === $beforeLogs, 'Failed acceptance must roll back its audit log.');

echo "phase-6 community/notification checks passed ({$checks} assertions)\n";
