<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/services/identity.php';
$database = $bootstrap['database'];
$jwt = checkAuth();

requirePermission(isElevatedAdmin($jwt));
$body = getJsonBody();
$errors = validateIdentityFields($body, 'member');
if (!empty($body['email']) && recordExists($database, 'member_profiles', ['email' => $body['email']])) {
        $errors['email'] = ['A profile with this email already exists.'];
    }
validateOrFail($errors);

$profile = databaseTransaction($database, static function () use ($database, $body, $jwt): array {
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
        $id = (int) $database->id();
        $profile = getProfile($database, $id);
        logActivity($database, $jwt, 'create', 'member_profiles', $id, null, $profile);
        return $profile;
    });

successResponse($profile, 'Member profile created successfully.', 201);

