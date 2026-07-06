<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Models\Admin;
use App\Modules\Families\Repositories\AdminRepository;
use App\Modules\Families\Repositories\MemberRepository;

class AdminService
{
    private const VALID_ROLES   = ['super_admin', 'program_manager', 'accounts', 'readonly'];
    private const VALID_STATUSES = ['active', 'inactive'];

    public function __construct(
        private readonly AdminRepository    $adminRepo,
        private readonly MemberRepository   $memberRepo,
        private readonly ActivityLogService $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->adminRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Admin
    {
        $errors = [];

        if (empty($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id field is required.'];
        }
        if (empty($data['admin_role'])) {
            $errors['admin_role'] = ['The admin_role field is required.'];
        } elseif (!in_array($data['admin_role'], self::VALID_ROLES, true)) {
            $errors['admin_role'] = ['The admin_role must be one of: super_admin, program_manager, accounts, readonly.'];
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $profileId = (int) $data['profile_id'];
        $profile   = $this->memberRepo->findById($profileId);
        if (!$profile) {
            throw new NotFoundException('Member profile not found.');
        }

        $existing = $this->adminRepo->findByProfileId($profileId);
        if ($existing) {
            throw new DuplicateException('This profile is already registered as an admin.');
        }

        $admin = $this->adminRepo->create([
            'profile_id' => $profileId,
            'admin_role' => $data['admin_role'],
            'status'     => 'active',
        ]);

        $admin->load('profile');

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'admins', $admin->id, null, $admin->toArray());

        return $admin;
    }

    public function show(int $id, array $jwt): Admin
    {
        $admin = $this->adminRepo->findWithProfile($id);
        if (!$admin) {
            throw new NotFoundException('Admin not found.');
        }
        return $admin;
    }

    public function update(int $id, array $data, array $jwt): Admin
    {
        $admin = $this->adminRepo->findWithProfile($id);
        if (!$admin) {
            throw new NotFoundException('Admin not found.');
        }

        $errors = [];
        if (!empty($data['admin_role']) && !in_array($data['admin_role'], self::VALID_ROLES, true)) {
            $errors['admin_role'] = ['The admin_role must be one of: super_admin, program_manager, accounts, readonly.'];
        }
        if (!empty($data['status']) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive.'];
        }
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues  = $admin->toArray();
        $updateData = array_intersect_key($data, array_flip(['admin_role', 'status']));
        $updated    = $this->adminRepo->update($admin, $updateData);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'admins', $id, $oldValues, $updated->toArray());

        return $updated->load('profile');
    }
}
