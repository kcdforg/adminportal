<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Auth\Models\MemberProfile;
use App\Modules\Families\Policies\MemberPolicy;
use App\Modules\Families\Repositories\MemberRepository;
use App\Modules\Families\Validators\MemberValidator;

class MemberService
{
    public function __construct(
        private readonly MemberRepository   $memberRepo,
        private readonly MemberPolicy       $memberPolicy,
        private readonly MemberValidator    $validator,
        private readonly ActivityLogService $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->memberRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): MemberProfile
    {
        $errors = $this->validator->validateCreate($data);

        if (!empty($data['email']) && $this->memberRepo->findByEmail($data['email'])) {
            $errors['email'] = ['A profile with this email already exists.'];
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $profile = $this->memberRepo->create([
            'first_name'    => $data['first_name'],
            'middle_name'   => $data['middle_name'] ?? null,
            'last_name'     => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'gender'        => $data['gender'] ?? null,
            'mobile'        => $data['mobile'] ?? null,
            'email'         => $data['email'] ?? null,
            'photo_url'     => $data['photo_url'] ?? null,
            'blood_group'   => $data['blood_group'] ?? null,
            'status'        => 'active',
        ]);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'member_profiles', $profile->id, null, $profile->toArray());

        return $profile;
    }

    public function show(int $id, array $jwt): MemberProfile
    {
        $profile = $this->memberRepo->findById($id);
        if (!$profile) {
            throw new NotFoundException('Member profile not found.');
        }

        if (!$this->memberPolicy->canView($jwt, $id)) {
            throw new UnauthorizedException();
        }

        return $profile;
    }

    public function update(int $id, array $data, array $jwt): MemberProfile
    {
        $profile = $this->memberRepo->findById($id);
        if (!$profile) {
            throw new NotFoundException('Member profile not found.');
        }

        if (!$this->memberPolicy->canEdit($jwt, $id)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateUpdate($data);

        if (!empty($data['email']) && $this->memberRepo->emailExistsExcept($data['email'], $id)) {
            $errors['email'] = ['A profile with this email already exists.'];
        }

        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $allowed = ['first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'mobile', 'email', 'photo_url', 'blood_group', 'status'];
        $updateData = array_intersect_key($data, array_flip($allowed));

        $oldValues = $profile->toArray();
        $updated   = $this->memberRepo->update($profile, $updateData);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'member_profiles', $id, $oldValues, $updated->toArray());

        return $updated;
    }
}
