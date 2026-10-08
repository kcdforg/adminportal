<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$familyId = (int) ($routeParams['id'] ?? 0);
$database = $bootstrap['database'];
$jwt = checkAuth();
if (!recordExists($database, 'families', ['id' => $familyId])) {
    errorResponse('NOT_FOUND', 'Family not found.', 404);
}
requirePermission(canAccessFamily($database, $jwt, $familyId, 'edit'));
$body = getJsonBody();
$errors = [];
if (empty($body['profile_id'])) {
    $errors['profile_id'] = ['The profile_id field is required.'];
} elseif (!is_numeric($body['profile_id'])) {
    $errors['profile_id'] = ['The profile_id must be a valid integer.'];
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
$profileId = (int) $body['profile_id'];
if (getProfile($database, $profileId) === null) {
    errorResponse('NOT_FOUND', 'Member profile not found.', 404);
}
$membership = databaseTransaction($database, static function () use ($database, $body, $familyId, $profileId, $jwt): array {
    if (!lockIdentityRecord($database, 'families', $familyId)) {
        throw new RuntimeException('Family not found.', 404);
    }
    if (getFamilyMembership($database, $familyId, $profileId) !== null) {
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
    $removed = getFamilyMembership($database, $familyId, $profileId, false);
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
            'profile_id' => $profileId,
            'relationship_type' => $body['relationship_type'],
            'member_role' => $body['member_role'],
            'status' => 'active',
        ]);
        $id = (int) $database->id();
    }
    $row = findOne($database, 'family_members', ['id' => $id]);
    $row['profile'] = getProfile($database, $profileId);
    logActivity($database, $jwt, 'add_member', 'families', $familyId, null, $row);
    return $row;
});
successResponse($membership, 'Member added to family successfully.', 201);

