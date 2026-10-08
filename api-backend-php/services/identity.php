<?php

declare(strict_types=1);

function getUserRoles(array $jwt): array
{
    return array_values(array_unique(array_map('strval', (array) ($jwt['roles'] ?? []))));
}

function isAdmin(array $jwt): bool
{
    return count(array_intersect(getUserRoles($jwt), [
        'admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly',
    ])) > 0;
}

function isElevatedAdmin(array $jwt): bool
{
    return count(array_intersect(getUserRoles($jwt), ['admin_super', 'admin_program_manager'])) > 0;
}

function requirePermission(bool $allowed): void
{
    if (!$allowed) {
        errorResponse('UNAUTHORIZED', 'You are not authorized to perform this action.', 403);
    }
}

function getUserId(array $jwt): int
{
    return (int) ($jwt['profile_id'] ?? 0);
}

function getActorId(array $jwt): ?int
{
    $id = getUserId($jwt);
    return $id > 0 ? $id : null;
}

function logActivity(Medoo\Medoo $database, array $jwt, string $action, string $type, int $id, ?array $old, ?array $new): void
{
    $database->insert('activity_logs', [
        'actor_profile_id' => getActorId($jwt),
        'action' => $action,
        'entity_type' => $type,
        'entity_id' => $id,
        'old_values' => $old === null ? null : json_encode($old, JSON_THROW_ON_ERROR),
        'new_values' => $new === null ? null : json_encode($new, JSON_THROW_ON_ERROR),
    ]);
}

function databaseTransaction(Medoo\Medoo $database, callable $callback)
{
    $pdo = $database->pdo;
    $pdo->beginTransaction();
    try {
        $result = $callback();
        $pdo->commit();
        return $result;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $exception;
    }
}

function isValidDate(string $value): bool
{
    $date = DateTime::createFromFormat('Y-m-d', $value);
    return $date !== false && $date->format('Y-m-d') === $value;
}

function validateOrFail(array $errors): void
{
    if ($errors !== []) {
        errorResponse('VALIDATION_FAILED', 'Validation failed', 422, $errors);
    }
}

function findOne(Medoo\Medoo $database, string $table, array $where, array $columns = ['*']): ?array
{
    $row = $database->get($table, $columns, $where);
    return is_array($row) ? castIds($row) : null;
}

function recordExists(Medoo\Medoo $database, string $table, array $where): bool
{
    return $database->has($table, $where);
}

function lockIdentityRecord(Medoo\Medoo $database, string $table, int $id): bool
{
    if (!in_array($table, ['families', 'member_profiles'], true)) {
        throw new InvalidArgumentException('Unsupported table requested for identity row locking.');
    }
    if ($database->type === 'sqlite') {
        return recordExists($database, $table, ['id' => $id]);
    }
    $statement = $database->pdo->prepare('SELECT id FROM ' . $table . ' WHERE id = :id FOR UPDATE');
    $statement->execute([':id' => $id]);
    return $statement->fetchColumn() !== false;
}

function findMany(Medoo\Medoo $database, string $table, array $columns = ['*'], array $where = []): array
{
    return array_map('castIds', $database->select($table, $columns, $where) ?? []);
}

function castIds(array $row): array
{
    foreach ($row as $key => $value) {
        if ($value !== null && ($key === 'id' || str_ends_with($key, '_id')) && ctype_digit((string) $value)) {
            $row[$key] = (int) $value;
        }
    }
    return $row;
}

function getProfileById(Medoo\Medoo $database, int $profileId): ?array
{
    $profile = $database->get('member_profiles', [
        'id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'mobile',
        'email',
        'photo_url',
        'blood_group',
        'status',
    ], ['id' => $profileId]);

    return is_array($profile) ? $profile : null;
}

function getProfileRoles(Medoo\Medoo $database, int $profileId): array
{
    $roles = [];
    $familyIds = [];

    $familyMembers = $database->select('family_members', ['family_id', 'member_role'], [
        'profile_id' => $profileId,
        'status' => 'active',
    ]) ?? [];

    foreach ($familyMembers as $row) {
        $familyIds[] = (int) $row['family_id'];
        $roles[] = match ((string) $row['member_role']) {
            'primary' => 'family_primary',
            'normal' => 'family_normal',
            'student' => 'family_student',
            default => null,
        };
    }

    $roles = array_filter($roles);

    if ($database->has('trainers', ['profile_id' => $profileId, 'status' => 'active'])) {
        $roles[] = 'trainer';
    }

    $adminRoles = $database->select('admins', 'admin_role', [
        'profile_id' => $profileId,
        'status' => 'active',
    ]) ?? [];

    $roleMap = [
        'super_admin' => 'admin_super',
        'program_manager' => 'admin_program_manager',
        'accounts' => 'admin_accounts',
        'readonly' => 'admin_readonly',
    ];

    foreach ($adminRoles as $adminRole) {
        if (isset($roleMap[$adminRole])) {
            $roles[] = $roleMap[$adminRole];
        }
    }

    $roles = array_values(array_unique($roles));
    sort($roles);
    $familyIds = array_values(array_unique($familyIds));
    sort($familyIds);

    return [
        'roles' => $roles,
        'family_ids' => $familyIds,
    ];
}

function getProfile(Medoo\Medoo $database, int $id): ?array
{
    return findOne($database, 'member_profiles', ['id' => $id]);
}

function getAddress(Medoo\Medoo $database, $id): ?array
{
    if (!$id) {
        return null;
    }
    return findOne($database, 'addresses', ['id' => $id]);
}

function getFamily(Medoo\Medoo $database, int $id): ?array
{
    $family = findOne($database, 'families', ['id' => $id]);
    if ($family !== null) {
        $family['address'] = getAddress($database, $family['address_id']);
    }
    return $family;
}

function getTrainerRecord(Medoo\Medoo $database, int $id): ?array
{
    $trainer = findOne($database, 'trainers', ['id' => $id]);
    if ($trainer !== null) {
        $trainer['profile'] = getProfile($database, (int) $trainer['profile_id']);
        $trainer['address'] = getAddress($database, $trainer['address_id']);
        if ($trainer['joined_at'] !== null) {
            $trainer['joined_at'] .= ' 00:00:00';
        }
    }
    return $trainer;
}

function getAdmin(Medoo\Medoo $database, int $id): ?array
{
    $admin = findOne($database, 'admins', ['id' => $id]);
    if ($admin !== null) {
        $admin['profile'] = getProfile($database, (int) $admin['profile_id']);
    }
    return $admin;
}

function getEntity(Medoo\Medoo $database, int $id): ?array
{
    $entity = findOne($database, 'entities', ['id' => $id]);
    if ($entity !== null && $entity['meta'] !== null) {
        $entity['meta'] = json_decode((string) $entity['meta'], true);
    }
    return $entity;
}

function getFamilyMembership(Medoo\Medoo $database, int $familyId, int $profileId, bool $activeOnly = true): ?array
{
    $where = ['family_id' => $familyId, 'profile_id' => $profileId];
    if ($activeOnly) {
        $where['status'] = 'active';
    }
    return findOne($database, 'family_members', $where);
}

function isPrimaryInSameFamily(Medoo\Medoo $database, int $requesterId, int $targetId): bool
{
    $families = $database->select('family_members', 'family_id', [
        'profile_id' => $requesterId,
        'member_role' => 'primary',
        'status' => 'active',
    ]) ?? [];
    if ($families === []) {
        return false;
    }
    return $database->has('family_members', [
        'family_id' => array_values(array_unique($families)),
        'profile_id' => $targetId,
        'status' => 'active',
    ]);
}

function canViewFamily(Medoo\Medoo $database, array $jwt, int $familyId): bool
{
    if (isAdmin($jwt)) {
        return true;
    }

    $membership = getFamilyMembership($database, $familyId, getUserId($jwt));
    if ($membership === null) {
        return false;
    }

    return $membership['member_role'] !== 'student';
}

function canEditFamily(Medoo\Medoo $database, array $jwt, int $familyId): bool
{
    if (isElevatedAdmin($jwt)) {
        return true;
    }

    $membership = getFamilyMembership($database, $familyId, getUserId($jwt));
    if ($membership === null) {
        return false;
    }

    return $membership['member_role'] === 'primary';
}

function canViewMember(Medoo\Medoo $database, array $jwt, int $profileId): bool
{
    if (isAdmin($jwt)) {
        return true;
    }

    $requesterId = getUserId($jwt);
    if ($requesterId === $profileId) {
        return true;
    }

    return isPrimaryInSameFamily($database, $requesterId, $profileId);
}

function canEditMember(Medoo\Medoo $database, array $jwt, int $profileId): bool
{
    if (isAdmin($jwt)) {
        return true;
    }

    return getUserId($jwt) === $profileId;
}

function canManageMemberRelations(Medoo\Medoo $database, array $jwt, int $profileId): bool
{
    return canViewMember($database, $jwt, $profileId)
        && isPrimaryInSameFamily($database, getUserId($jwt), $profileId);
}

function canAccessFamily(Medoo\Medoo $database, array $jwt, int $familyId, string $permission): bool
{
    return match ($permission) {
        'view' => canViewFamily($database, $jwt, $familyId),
        'edit' => canEditFamily($database, $jwt, $familyId),
        default => false,
    };
}

function canAccessMember(Medoo\Medoo $database, array $jwt, int $profileId, string $permission): bool
{
    return match ($permission) {
        'view' => canViewMember($database, $jwt, $profileId),
        'edit' => canEditMember($database, $jwt, $profileId),
        'manage_relations' => canManageMemberRelations($database, $jwt, $profileId),
        default => isPrimaryInSameFamily($database, getUserId($jwt), $profileId),
    };
}

function validateTextField(
    array &$errors,
    string $field,
    $value,
    int $maxLength,
    string $requiredMessage,
    string $emptyMessage,
    string $tooLongMessage,
    bool $isRequired = true
): void {
    if ($value === null || $value === [] || $value === false) {
        if ($isRequired) {
            $errors[$field] = [$requiredMessage];
        }
        return;
    }

    $stringValue = trim((string) $value);
    if ($stringValue === '') {
        $errors[$field] = [$emptyMessage];
        return;
    }

    if (strlen((string) $value) > $maxLength) {
        $errors[$field] = [$tooLongMessage];
    }
}

function validateChoice(array &$errors, string $field, $value, array $allowed, string $errorMessage): void
{
    if ($value === null || $value === '' || $value === []) {
        return;
    }

    if (!in_array($value, $allowed, true)) {
        $errors[$field] = [$errorMessage];
    }
}

function validateRequired(array &$errors, string $field, $value, string $message): void
{
    if (empty($value)) {
        $errors[$field] = [$message];
    }
}

function validatePositiveInt(array &$errors, string $field, $value, string $message): void
{
    if ($value === null || $value === '' || $value === [] || $value === false) {
        return;
    }

    if (!is_numeric($value) || (int) $value <= 0) {
        $errors[$field] = [$message];
    }
}

function validateNonNegativeNumber(array &$errors, string $field, $value, string $message, ?string $decimalMessage = null): void
{
    if ($value === null || $value === '' || $value === [] || $value === false) {
        return;
    }

    if (!is_numeric($value) || (float) $value < 0) {
        $errors[$field] = [$message];
        return;
    }

    if ($decimalMessage !== null && is_numeric($value)) {
        $numeric = (string) $value;
        $decimalIndex = strpos($numeric, '.');
        if ($decimalIndex !== false) {
            $decimalPlaces = strlen(substr($numeric, $decimalIndex + 1));
            if ($decimalPlaces > 2) {
                $errors[$field] = [$decimalMessage];
            }
        }
    }
}

function validateLength(array &$errors, string $field, $value, int $maxLength, string $requiredMessage, string $tooLongMessage): void
{
    validateTextField(
        $errors,
        $field,
        $value,
        $maxLength,
        $requiredMessage,
        $requiredMessage,
        $tooLongMessage,
        true
    );
}

function validateEmail(array &$errors, string $field, $value, string $message = 'The email must be a valid email address.'): void
{
    if ($value === null || $value === '' || $value === []) {
        return;
    }

    if (!filter_var((string) $value, FILTER_VALIDATE_EMAIL)) {
        $errors[$field] = [$message];
    }
}

function validateMobile(array &$errors, string $field, $value, string $invalidMessage = 'The mobile must contain digits only.', string $shortMessage = 'The mobile must be at least 10 digits.'): void
{
    if ($value === null || $value === '' || $value === []) {
        return;
    }

    $digits = (string) $value;
    if (!ctype_digit($digits)) {
        $errors[$field] = [$invalidMessage];
        return;
    }

    if (strlen($digits) < 10) {
        $errors[$field] = [$shortMessage];
    }
}

function validateDate(array &$errors, string $field, $value, string $message = 'The date must be a valid date (YYYY-MM-DD).'): void
{
    if ($value === null || $value === '' || $value === []) {
        return;
    }

    if (!isValidDate((string) $value)) {
        $errors[$field] = [$message];
    }
}

function validateTime(array &$errors, string $field, $value, string $message = 'The time must be a valid time (HH:MM).'): void
{
    if ($value === null || $value === '' || $value === []) {
        return;
    }

    if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) $value)) {
        $errors[$field] = [$message];
    }
}

function validateAddress($address): array
{
    if ($address === null || $address === []) {
        return [];
    }
    if (!is_array($address)) {
        return ['address' => ['The address must be an object.']];
    }
    $errors = [];
    foreach (['address_line_1', 'city', 'country'] as $field) {
        if (empty($address[$field])) {
            $errors['address.' . $field] = ['The address.' . $field . ' field is required when address is provided.'];
        }
    }
    return $errors;
}

function createAddress(Medoo\Medoo $database, array $data): int
{
    $database->insert('addresses', [
        'address_label' => $data['address_label'] ?? 'home',
        'address_line_1' => $data['address_line_1'],
        'address_line_2' => $data['address_line_2'] ?? null,
        'city' => $data['city'],
        'state' => $data['state'] ?? null,
        'postal_code' => $data['postal_code'] ?? null,
        'country' => $data['country'] ?? 'India',
    ]);
    return (int) $database->id();
}

function updateAddress(Medoo\Medoo $database, int $id, array $data): void
{
    $allowed = ['address_label', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country'];
    $update = [];
    foreach ($allowed as $field) {
        if (array_key_exists($field, $data)) {
            $update[$field] = $data[$field];
        }
    }
    if ($update !== []) {
        $database->update('addresses', $update, ['id' => $id]);
    }
}

function validateIdentityFields(array $data, string $kind, bool $update = false): array
{
    $errors = [];
    $requiredText = static function (string $field, string $requiredMessage, int $maxLength, string $tooLongMessage) use (&$errors, $data, $update): void {
        validateTextField($errors, $field, $data[$field] ?? null, $maxLength, $requiredMessage, $requiredMessage, $tooLongMessage, !$update);
    };

    if ($kind === 'member') {
        foreach (['first_name', 'last_name'] as $field) {
            $requiredText($field, "The {$field} field is required.", 100, "The {$field} may not be greater than 100 characters.");
            if (array_key_exists($field, $data) && $data[$field] !== null && $data[$field] !== '' && $data[$field] !== []) {
                $trimmed = trim((string) $data[$field]);
                if ($trimmed === '') {
                    $errors[$field] = ["The {$field} field cannot be empty."];
                }
            }
        }
        validateEmail($errors, 'email', $data['email'] ?? null);
        validateMobile($errors, 'mobile', $data['mobile'] ?? null);
        validateChoice($errors, 'gender', $data['gender'] ?? null, ['male', 'female', 'other'], 'The gender must be one of: male, female, other.');
        validateChoice($errors, 'status', $data['status'] ?? null, ['active', 'inactive', 'suspended'], 'The status must be one of: active, inactive, suspended.');
        if (!empty($data['date_of_birth'])) {
            if (!isValidDate((string) $data['date_of_birth'])) {
                $errors['date_of_birth'] = ['The date_of_birth must be a valid date (YYYY-MM-DD).'];
            } elseif (new DateTime((string) $data['date_of_birth']) > new DateTime('today')) {
                $errors['date_of_birth'] = ['The date_of_birth must not be in the future.'];
            }
        }
    } elseif ($kind === 'family') {
        $requiredText('family_name', 'The family_name field is required.', 255, 'The family_name may not be greater than 255 characters.');
        if (array_key_exists('family_name', $data) && $data['family_name'] !== null && $data['family_name'] !== [] && $data['family_name'] !== false && trim((string) $data['family_name']) === '') {
            $errors['family_name'] = ['The family_name field cannot be empty.'];
        }
        validateChoice($errors, 'status', $data['status'] ?? null, ['active', 'inactive'], 'The status must be one of: active, inactive.');
        if (!empty($data['address'])) {
            $errors = array_merge($errors, validateAddress($data['address']));
        }
    } elseif ($kind === 'trainer') {
        if (!$update && empty($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id field is required.'];
        } elseif (!$update && !is_numeric($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id must be a valid integer.'];
        }
        if (isset($data['specialization'])) {
            validateLength($errors, 'specialization', $data['specialization'], 255, 'The specialization field is required.', 'The specialization may not be greater than 255 characters.');
        }
        if (array_key_exists('experience_years', $data) && $data['experience_years'] !== null
            && (!is_numeric($data['experience_years']) || (int) $data['experience_years'] < 0 || (int) $data['experience_years'] > 60)) {
            $errors['experience_years'] = ['The experience_years must be an integer between 0 and 60.'];
        }
        if (!empty($data['joined_at'])) {
            validateDate($errors, 'joined_at', $data['joined_at'], 'The joined_at must be a valid date (YYYY-MM-DD).');
        }
        if ($update) {
            validateChoice($errors, 'status', $data['status'] ?? null, ['active', 'inactive', 'on_leave'], 'The status must be one of: active, inactive, on_leave.');
        }
        if (!empty($data['address'])) {
            $errors = array_merge($errors, validateAddress($data['address']));
        }
    } elseif ($kind === 'entity') {
        $types = ['school', 'college', 'organization', 'hospital', 'association'];
        if (!$update && empty($data['entity_type'])) {
            $errors['entity_type'] = ['The entity_type field is required.'];
        }
        validateChoice($errors, 'entity_type', $data['entity_type'] ?? null, $types, 'The entity_type must be one of: school, college, organization, hospital, association.');
        if (!$update && empty($data['name'])) {
            $errors['name'] = ['The name field is required.'];
        } elseif (array_key_exists('name', $data) && empty($data['name'])) {
            $errors['name'] = ['The name field cannot be empty.'];
        } elseif (isset($data['name'])) {
            validateLength($errors, 'name', $data['name'], 255, 'The name field is required.', 'The name may not be greater than 255 characters.');
        }
        if ($update) {
            validateChoice($errors, 'status', $data['status'] ?? null, ['active', 'inactive'], 'The status must be one of: active, inactive.');
        }
    }
    return $errors;
}

function paginate(Medoo\Medoo $database, string $table, array $filters, array $filterColumns, array $sortColumns, array $searchColumns = []): array
{
    $where = [];
    foreach ($filterColumns as $name => $column) {
        if (isset($filters[$name]) && $filters[$name] !== '') {
            $where[$column] = $filters[$name];
        }
    }
    if (isset($filters['search']) && $filters['search'] !== '' && $searchColumns !== []) {
        $search = [];
        foreach ($searchColumns as $column) {
            $search[$column . '[~]'] = $filters['search'];
        }
        $where['OR'] = $search;
    }
    $total = (int) $database->count($table, $where);
    $perPage = min(max((int) ($filters['per_page'] ?? 20), 1), 100);
    $page = max((int) ($filters['page'] ?? 1), 1);
    $sort = in_array($filters['sort'] ?? '', $sortColumns, true) ? $filters['sort'] : 'created_at';
    $order = strtolower((string) ($filters['order'] ?? 'desc')) === 'asc' ? 'ASC' : 'DESC';
    $offset = ($page - 1) * $perPage;
    $rows = $database->select($table, '*', array_merge($where, [
        'ORDER' => [$sort => $order],
        'LIMIT' => [$offset, $perPage],
    ])) ?? [];

    return [
        'data' => array_map('castIds', $rows),
        'meta' => [
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => max(1, (int) ceil($total / $perPage)),
        ],
    ];
}
