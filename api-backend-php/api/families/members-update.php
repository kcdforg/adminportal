<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$familyId = (int) ($routeParams['id'] ?? 0);
$profileId = (int) ($routeParams['profile_id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();

if (!recordExists($database, 'families', ['id' => $familyId])) {
    errorResponse('NOT_FOUND', 'Family not found.', 404);
}
requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));
$membership = getFamilyMembership($database, $familyId, $profileId);
if ($membership === null) {
    errorResponse('NOT_FOUND', 'Member is not part of this family.', 404);
}

$body = getJsonBody();
$errors = validateIdentityFields($body, 'member', true);
$relationships = ['father', 'mother', 'guardian', 'child'];
$memberRoles = ['primary', 'normal', 'student'];
if (isset($body['relationship_type']) && !in_array($body['relationship_type'], $relationships, true)) {
    $errors['relationship_type'] = ['The relationship_type must be one of: father, mother, guardian, child.'];
}
if (isset($body['member_role']) && !in_array($body['member_role'], $memberRoles, true)) {
    $errors['member_role'] = ['The member_role must be one of: primary, normal, student.'];
}
if (isset($body['email']) && $body['email'] !== '' && $database->has('member_profiles', [
    'email' => $body['email'],
    'id[!]' => $profileId,
])) {
    $errors['email'] = ['A profile with this email already exists.'];
}
validateOrFail($errors);

$updated = databaseTransaction($database, static function () use ($database, $body, $membership, $familyId, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'families', $familyId)) {
        throw new RuntimeException('Family not found.', 404);
    }
    $profileFields = [
        'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender',
        'mobile', 'email', 'photo_url', 'blood_group', 'status',
    ];
    $profileUpdate = [];
    foreach ($profileFields as $field) {
        if (array_key_exists($field, $body)) {
            $profileUpdate[$field] = $body[$field];
        }
    }
    $membershipUpdate = [];
    foreach (['relationship_type', 'member_role'] as $field) {
        if (array_key_exists($field, $body)) {
            $membershipUpdate[$field] = $body[$field];
        }
    }

    if (($membershipUpdate['member_role'] ?? $membership['member_role']) === 'primary'
        && $membership['member_role'] !== 'primary'
        && recordExists($database, 'family_members', [
            'family_id' => $familyId,
            'member_role' => 'primary',
            'status' => 'active',
        ])) {
        throw new RuntimeException('This family already has a primary member. Only one primary member is allowed.', 409);
    }

    if ($profileUpdate !== []) {
        $database->update('member_profiles', $profileUpdate, ['id' => $profileId]);
    }
    if ($membershipUpdate !== []) {
        $database->update('family_members', $membershipUpdate, ['id' => $membership['id']]);
    }

    $row = findOne($database, 'family_members', ['id' => $membership['id']]);
    $row['profile'] = getProfile($database, $profileId);
    $login = $database->get('user_logins', [
        'id', 'profile_id', 'username', 'display_name', 'is_active', 'last_login_at',
    ], ['profile_id' => $profileId, 'user_type' => 'member']);
    $row['login'] = is_array($login) ? castIds($login) : null;
    logActivity($database, $jwt, 'update_member', 'families', $familyId, $membership, $row);
    return $row;
});

successResponse($updated, 'Family member updated successfully.');
