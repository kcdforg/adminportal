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
databaseTransaction($database, static function () use ($database, $membership, $familyId, $jwt): void {
    $database->update('family_members', ['status' => 'removed'], ['id' => $membership['id']]);
    logActivity($database, $jwt, 'remove_member', 'families', $familyId, $membership, ['status' => 'removed']);
});
successResponse(null, 'Member removed from family successfully.');

