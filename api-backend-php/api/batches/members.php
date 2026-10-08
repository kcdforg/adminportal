<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/academics.php';
$id = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
$batch = getBatch($database, $id, false);
if ($batch === null) errorResponse('NOT_FOUND', 'Batch not found.', 404);
requirePermission(canAccessBatch($database, $jwt, $batch, 'members'));
$members = $database->select('batch_members', ['[>]member_profiles' => ['member_id' => 'id']], [
    'batch_members.id', 'batch_members.batch_id', 'batch_members.member_id',
    'batch_members.joined_at', 'batch_members.status', 'batch_members.created_at', 'batch_members.updated_at',
    'member_profiles.id(profile_id)', 'member_profiles.first_name', 'member_profiles.middle_name',
    'member_profiles.last_name', 'member_profiles.date_of_birth', 'member_profiles.gender',
    'member_profiles.mobile', 'member_profiles.email', 'member_profiles.photo_url',
    'member_profiles.blood_group', 'member_profiles.status(profile_status)',
    'member_profiles.created_at(profile_created_at)', 'member_profiles.updated_at(profile_updated_at)',
], ['batch_members.batch_id' => $id]) ?? [];
foreach ($members as &$member) {
    $profileId = (int) $member['profile_id'];
    $member['id'] = (int) $member['id'];
    $member['batch_id'] = (int) $member['batch_id'];
    $member['member_id'] = (int) $member['member_id'];
    $member['member'] = [
        'id' => $profileId, 'first_name' => $member['first_name'], 'middle_name' => $member['middle_name'],
        'last_name' => $member['last_name'], 'date_of_birth' => $member['date_of_birth'], 'gender' => $member['gender'],
        'mobile' => $member['mobile'], 'email' => $member['email'], 'photo_url' => $member['photo_url'],
        'blood_group' => $member['blood_group'], 'status' => $member['profile_status'],
        'created_at' => $member['profile_created_at'], 'updated_at' => $member['profile_updated_at'],
    ];
    foreach (['profile_id', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group', 'profile_status', 'profile_created_at', 'profile_updated_at'] as $field) unset($member[$field]);
}
unset($member);
successResponse($members);

