<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$familyId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

if (!recordExists($database, 'families', ['id' => $familyId])) {
        errorResponse('NOT_FOUND', 'Family not found.', 404);
    }

requirePermission(canAccessFamily($database, $jwt, $familyId, 'view'));

$rows = $database->select('family_members', [
        '[>]member_profiles' => ['profile_id' => 'id'],
    ], [
        'family_members.id',
        'family_members.family_id',
        'family_members.profile_id',
        'family_members.relationship_type',
        'family_members.member_role',
        'family_members.status',
        'family_members.created_at',
        'family_members.updated_at',
        'member_profiles.first_name',
        'member_profiles.middle_name',
        'member_profiles.last_name',
        'member_profiles.date_of_birth',
        'member_profiles.gender',
        'member_profiles.mobile',
        'member_profiles.email',
        'member_profiles.photo_url',
        'member_profiles.blood_group',
        'member_profiles.created_at(profile_created_at)',
        'member_profiles.updated_at(profile_updated_at)',
        'member_profiles.status(profile_status)',
    ], [
        'family_members.family_id' => $familyId,
        'family_members.status' => 'active',
    ]) ?? [];

$members = [];
foreach ($rows as $row) {
        $members[] = [
            'id' => (int) $row['id'],
            'family_id' => (int) $row['family_id'],
            'profile_id' => (int) $row['profile_id'],
            'relationship_type' => $row['relationship_type'],
            'member_role' => $row['member_role'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'updated_at' => $row['updated_at'],
            'profile' => [
                'id' => (int) $row['profile_id'],
                'first_name' => $row['first_name'],
                'middle_name' => $row['middle_name'],
                'last_name' => $row['last_name'],
                'date_of_birth' => $row['date_of_birth'],
                'gender' => $row['gender'],
                'mobile' => $row['mobile'],
                'email' => $row['email'],
                'photo_url' => $row['photo_url'],
                'blood_group' => $row['blood_group'],
                'status' => $row['profile_status'],
                'created_at' => $row['profile_created_at'],
                'updated_at' => $row['profile_updated_at'],
            ],
        ];
    }

successResponse($members);

