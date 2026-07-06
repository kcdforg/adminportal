<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Models\Family;
use App\Modules\Families\Models\FamilyMember;
use App\Modules\Families\Policies\FamilyPolicy;
use App\Modules\Families\Repositories\FamilyMemberRepository;
use App\Modules\Families\Repositories\FamilyRepository;
use App\Modules\Families\Repositories\MemberRepository;
use App\Modules\Families\Validators\FamilyValidator;
use Illuminate\Database\Capsule\Manager as DB;

class FamilyService
{
    public function __construct(
        private readonly FamilyRepository       $familyRepo,
        private readonly FamilyMemberRepository $familyMemberRepo,
        private readonly MemberRepository       $memberRepo,
        private readonly AddressService         $addressService,
        private readonly FamilyPolicy           $familyPolicy,
        private readonly FamilyValidator        $validator,
        private readonly ActivityLogService     $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->familyRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Family
    {
        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        return DB::transaction(function () use ($data, $jwt) {
            $addressId = null;
            if (!empty($data['address'])) {
                $address   = $this->addressService->createAddress($data['address']);
                $addressId = $address->id;
            }

            $family = Family::create([
                'family_code' => 'KCDF-TEMP-' . time(),
                'family_name' => $data['family_name'],
                'address_id'  => $addressId,
                'status'      => 'active',
            ]);

            $family->update(['family_code' => FamilyRepository::generateFamilyCode($family->id)]);
            $family = $this->familyRepo->findWithAddress($family->id);

            $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
            $this->activityLog->log($actorId, 'create', 'families', $family->id, null, $family->toArray());

            return $family;
        });
    }

    public function show(int $id, array $jwt): Family
    {
        $family = $this->familyRepo->findWithAddress($id);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->familyPolicy->canView($jwt, $id)) {
            throw new UnauthorizedException();
        }

        return $family;
    }

    public function update(int $id, array $data, array $jwt): Family
    {
        $family = $this->familyRepo->findWithAddress($id);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->familyPolicy->canEdit($jwt, $id)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues = $family->toArray();

        return DB::transaction(function () use ($id, $data, $family, $oldValues, $jwt) {
            if (!empty($data['address'])) {
                if ($family->address_id) {
                    $this->addressService->updateAddress($family->address, $data['address']);
                } else {
                    $address = $this->addressService->createAddress($data['address']);
                    $family->update(['address_id' => $address->id]);
                }
            }

            $updateData = array_intersect_key($data, array_flip(['family_name', 'status']));
            if (!empty($updateData)) {
                $this->familyRepo->update($family, $updateData);
            }

            $updated = $this->familyRepo->findWithAddress($id);

            $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
            $this->activityLog->log($actorId, 'update', 'families', $id, $oldValues, $updated->toArray());

            return $updated;
        });
    }

    public function listMembers(int $familyId, array $jwt): array
    {
        $family = $this->familyRepo->findById($familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->familyPolicy->canViewMembers($jwt, $familyId)) {
            throw new UnauthorizedException();
        }

        $members = $this->familyMemberRepo->getMembersForFamily($familyId);
        return $members->toArray();
    }

    public function addMember(int $familyId, array $data, array $jwt): FamilyMember
    {
        $family = $this->familyRepo->findById($familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->familyPolicy->canManageMembers($jwt, $familyId)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateAddMember($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $profileId = (int) $data['profile_id'];
        $profile   = $this->memberRepo->findById($profileId);
        if (!$profile) {
            throw new NotFoundException('Member profile not found.');
        }

        $existing = $this->familyMemberRepo->findByFamilyAndProfile($familyId, $profileId);
        if ($existing) {
            throw new DuplicateException('This profile is already a member of this family.');
        }

        if ($data['member_role'] === 'primary') {
            $existingPrimary = $this->familyMemberRepo->findPrimaryMember($familyId);
            if ($existingPrimary) {
                throw new DuplicateException('This family already has a primary member. Only one primary member is allowed.');
            }
        }

        $membership = $this->familyMemberRepo->create([
            'family_id'         => $familyId,
            'profile_id'        => $profileId,
            'relationship_type' => $data['relationship_type'],
            'member_role'       => $data['member_role'],
        ]);

        $membership->load('profile');

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'add_member', 'families', $familyId, null, $membership->toArray());

        return $membership;
    }

    public function removeMember(int $familyId, int $profileId, array $jwt): void
    {
        $family = $this->familyRepo->findById($familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        if (!$this->familyPolicy->canManageMembers($jwt, $familyId)) {
            throw new UnauthorizedException();
        }

        $membership = $this->familyMemberRepo->findByFamilyAndProfile($familyId, $profileId);
        if (!$membership) {
            throw new NotFoundException('Member is not part of this family.');
        }

        $oldValues = $membership->toArray();
        $membership->delete();

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'remove_member', 'families', $familyId, $oldValues, null);
    }
}
