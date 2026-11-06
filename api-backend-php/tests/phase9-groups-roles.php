<?php

declare(strict_types=1);

require __DIR__ . '/../config/autoload.php';
require __DIR__ . '/../services/community-notifications.php';

if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
    fwrite(STDERR, "phase-9 checks require PDO_SQLITE\n");
    exit(2);
}

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
$database = new Medoo\Medoo(['pdo' => $pdo, 'type' => 'sqlite', 'database' => ':memory:']);
$pdo->exec('CREATE TABLE parent_groups (id INTEGER PRIMARY KEY, category_id INTEGER NULL, group_name TEXT NOT NULL, visibility TEXT NOT NULL, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE group_categories (id INTEGER PRIMARY KEY, category_name TEXT NOT NULL UNIQUE, description TEXT NULL, status TEXT NOT NULL)');
$pdo->exec('CREATE TABLE group_members (id INTEGER PRIMARY KEY, group_id INTEGER NOT NULL, member_id INTEGER NOT NULL, joined_at TEXT NOT NULL, status TEXT NOT NULL, member_role TEXT NOT NULL DEFAULT \'member\', UNIQUE (group_id, member_id))');
$pdo->exec("INSERT INTO parent_groups VALUES (1, NULL, 'Private group', 'private', 'active'), (2, NULL, 'Public group', 'public', 'active'), (3, NULL, 'Archived group', 'invite_only', 'archived')");
$pdo->exec("INSERT INTO group_categories VALUES (1, 'Arts', NULL, 'active'), (2, 'Inactive', NULL, 'inactive')");
$pdo->exec("INSERT INTO group_members VALUES
    (1, 1, 10, '2026-01-01 00:00:00', 'active', 'owner'),
    (2, 1, 11, '2026-01-01 00:00:00', 'active', 'owner'),
    (3, 1, 12, '2026-01-01 00:00:00', 'active', 'admin'),
    (4, 1, 13, '2026-01-01 00:00:00', 'active', 'moderator'),
    (5, 1, 14, '2026-01-01 00:00:00', 'active', 'member'),
    (6, 2, 15, '2026-01-01 00:00:00', 'active', 'owner'),
    (7, 3, 16, '2026-01-01 00:00:00', 'active', 'member')");

$checks = 0;
$assert = static function (bool $condition, string $message) use (&$checks): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
};
$ownerOne = ['profile_id' => 10, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['family_primary']];
$ownerTwo = ['profile_id' => 11, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['family_primary']];
$groupAdmin = ['profile_id' => 12, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['family_primary']];
$moderator = ['profile_id' => 13, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['family_primary']];
$member = ['profile_id' => 14, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['family_primary']];
$platformAdmin = ['profile_id' => null, 'user_type' => 'admin', 'portal' => 'admin', 'roles' => ['admin_readonly']];
$forgedMemberAdmin = ['profile_id' => 14, 'user_type' => 'member', 'portal' => 'member', 'roles' => ['admin_super', 'family_primary']];
$privateGroup = ['id' => 1, 'visibility' => 'private', 'status' => 'active'];
$archivedGroup = ['id' => 3, 'visibility' => 'invite_only', 'status' => 'archived'];

$assert(isCommunityAdmin($platformAdmin), 'Authenticated platform admins must be recognized by platform identity.');
$assert(!isCommunityAdmin($forgedMemberAdmin), 'Member roles must not grant platform-admin access.');
$assert(canAccessGroup($database, $platformAdmin, $archivedGroup, 'settings'), 'Admins must administer archived/private groups without membership.');
$assert(canAccessGroup($database, $ownerOne, $privateGroup, 'settings'), 'Owners must manage group settings.');
$assert(canAccessGroup($database, $groupAdmin, $privateGroup, 'settings'), 'Group admins must manage routine settings.');
$assert(!canAccessGroup($database, $moderator, $privateGroup, 'settings'), 'Moderators must not manage group settings.');
$assert(!canAccessGroup($database, $member, $privateGroup, 'settings'), 'Ordinary members must not manage group settings.');
$assert(canAccessGroup($database, $member, $privateGroup, 'members_view'), 'Active members may view the group member list.');
$assert(!canAccessGroup($database, $member, $privateGroup, 'members_manage'), 'Viewing members must not grant member-management permissions.');
$assert(canAccessGroup($database, $moderator, $privateGroup, 'members_manage'), 'Moderators have limited member-management permission.');
$assert(canAssignGroupOwners($database, $ownerOne, $privateGroup), 'Every owner may manage ownership.');
$assert(canAssignGroupOwners($database, $ownerTwo, $privateGroup), 'Multiple owners must have equal ownership permissions.');
$assert(!canAssignGroupOwners($database, $groupAdmin, $privateGroup), 'Group admins cannot manage owners.');
$assert(canAddGroupMember($database, $ownerOne, $privateGroup, 'owner'), 'Owners may add another owner.');
$assert(canAddGroupMember($database, $groupAdmin, $privateGroup, 'admin'), 'Group admins may assign non-owner roles when adding members.');
$assert(!canAddGroupMember($database, $groupAdmin, $privateGroup, 'owner'), 'Group admins cannot assign ownership to new members.');
$assert(canChangeGroupMemberRole($database, $groupAdmin, $privateGroup, 'member', 'admin'), 'Group admins may assign non-owner group roles.');
$assert(!canChangeGroupMemberRole($database, $groupAdmin, $privateGroup, 'owner', 'member'), 'Group admins cannot demote owners.');
$assert(!canChangeGroupMemberRole($database, $groupAdmin, $privateGroup, 'member', 'owner'), 'Group admins cannot assign ownership.');
$assert(canChangeGroupMemberRole($database, $ownerOne, $privateGroup, 'member', 'owner'), 'Owners may assign ownership.');
$assert(canRemoveGroupMember($database, $moderator, $privateGroup, 'member', 'remove'), 'Moderators may remove ordinary members.');
$assert(!canRemoveGroupMember($database, $moderator, $privateGroup, 'member', 'ban'), 'Moderators may not ban members.');
$assert(!canRemoveGroupMember($database, $groupAdmin, $privateGroup, 'owner', 'remove'), 'Group admins may not remove owners.');
$assert(canRemoveGroupMember($database, $platformAdmin, $archivedGroup, 'owner', 'ban'), 'Platform admins may manage any member role.');
$assert(!canAccessGroup($database, $member, $archivedGroup, 'view'), 'A nonmember must not view an invite-only group.');

assertGroupOwnerRemains($database, 1, ['member_role' => 'owner'], 'active');
try {
    assertGroupOwnerRemains($database, 2, ['member_role' => 'owner'], 'active');
    $assert(false, 'The only owner must not leave an active group ownerless.');
} catch (KcdfCommunityException $exception) {
    $assert($exception->errorCode === 'LAST_GROUP_OWNER', 'Last-owner protection must report the standard conflict code.');
}

$assert(validateGroup(['group_name' => 'New group', 'visibility' => 'public', 'owner_member_id' => 22], 'create') === [], 'A new group with an initial owner should validate.');
$assert(validateGroup(['group_name' => 'Ownerless group'], 'create') === [], 'Group creation only requires a group name.');
$assert(validateGroup(['group_name' => ' ', 'display_name' => 'Optional label'], 'create') !== [], 'The group name must not be blank.');
$assert(validateGroup(['group_name' => 'Invalid category', 'visibility' => 'public', 'owner_member_id' => 22, 'category_id' => -1], 'create') !== [], 'Invalid category ids must be rejected.');
$assert(validateGroupCategory(['category_name' => 'New category'], 'create') === [], 'Valid categories should pass validation.');
$assert(validateGroupCategory(['category_name' => 'Bad category', 'status' => 'deleted'], 'status') !== [], 'Invalid category statuses must be rejected.');
$assert(isValidGroupMemberRole('owner') && isValidGroupMemberRole('moderator'), 'Supported group roles should be accepted.');
$assert(!isValidGroupMemberRole('platform_admin'), 'Invalid group roles must be rejected.');

echo "phase-9 group role checks passed ({$checks} assertions)\n";
