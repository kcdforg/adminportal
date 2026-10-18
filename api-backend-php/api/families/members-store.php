<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$familyId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!recordExists($database, 'families', ['id' => $familyId])) {
    errorResponse('NOT_FOUND', 'Family not found.', 404);
}
requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
$profileId = null;
if (isset($body['profile_id'])) {
    if (filter_var($body['profile_id'], FILTER_VALIDATE_INT) === false || (int) $body['profile_id'] < 1) {
        validateOrFail(['profile_id' => ['The profile_id must be a positive integer.']]);
    }
    $profileId = (int) $body['profile_id'];
    $errors = [];
} else {
    $errors = validateIdentityFields($body, 'member');
}
$relationships = ['father', 'mother', 'guardian', 'child'];
if (empty($body['relationship_type'])) {
    $errors['relationship_type'] = ['The relationship_type field is required.'];
} elseif (!in_array($body['relationship_type'], $relationships, true)) {
    $errors['relationship_type'] = ['The relationship_type must be one of: father, mother, guardian, child.'];
}
$roles = ['primary', 'normal', 'student'];
if (empty($body['member_role'])) {
    $errors['member_role'] = ['The member_role field is required.'];
} elseif (!in_array($body['member_role'], $roles, true)) {
    $errors['member_role'] = ['The member_role must be one of: primary, normal, student.'];
}
validateOrFail($errors);
if ($profileId !== null && getProfile($database, $profileId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
$membership = databaseTransaction($database, static function () use ($database, $body, $familyId, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'families', $familyId)) {
        throw new RuntimeException('Family not found.', 404);
    }
    $memberProfileId = $profileId;
    if ($memberProfileId === null) {
        if (!empty($body['email']) && recordExists($database, 'member_profiles', ['email' => $body['email']])) {
            throw new RuntimeException('A profile with this email already exists.', 409);
        }
        $database->insert('member_profiles', [
            'first_name' => $body['first_name'],
            'middle_name' => $body['middle_name'] ?? null,
            'last_name' => $body['last_name'],
            'date_of_birth' => $body['date_of_birth'] ?? null,
            'gender' => $body['gender'] ?? null,
            'mobile' => $body['mobile'] ?? null,
            'email' => $body['email'] ?? null,
            'photo_url' => $body['photo_url'] ?? null,
            'blood_group' => $body['blood_group'] ?? null,
            'status' => 'active',
        ]);
        $memberProfileId = (int) $database->id();
        logActivity($database, $jwt, 'create', 'member_profiles', $memberProfileId, null, getProfile($database, $memberProfileId));
    }
    if (getFamilyMembership($database, $familyId, $memberProfileId) !== null) {
        throw new RuntimeException('This profile is already a member of this family.', 409);
    }
    if ($body['member_role'] === 'primary'
        && recordExists($database, 'family_members', [
            'family_id' => $familyId,
            'member_role' => 'primary',
            'status' => 'active',
        ])) {
        throw new RuntimeException('This family already has a primary member. Only one primary member is allowed.', 409);
    }
    $removed = getFamilyMembership($database, $familyId, $memberProfileId, false);
    if ($removed !== null && $removed['status'] === 'removed') {
        $database->update('family_members', [
            'relationship_type' => $body['relationship_type'],
            'member_role' => $body['member_role'],
            'status' => 'active',
        ], ['id' => $removed['id']]);
        $id = (int) $removed['id'];
    } else {
        $database->insert('family_members', [
            'family_id' => $familyId,
            'profile_id' => $memberProfileId,
            'relationship_type' => $body['relationship_type'],
            'member_role' => $body['member_role'],
            'status' => 'active',
        ]);
        $id = (int) $database->id();
    }
    $row = findOne($database, 'family_members', ['id' => $id]);
    $row['profile'] = getProfile($database, $memberProfileId);
    $login = $database->get('user_logins', [
        'id', 'profile_id', 'username', 'display_name', 'is_active', 'last_login_at',
    ], ['profile_id' => $memberProfileId, 'user_type' => 'member']);
    $row['login'] = is_array($login) ? castIds($login) : null;
    logActivity($database, $jwt, 'add_member', 'families', $familyId, null, $row);
    return $row;
});
successResponse($membership, 'Member added to family successfully.', 201);
