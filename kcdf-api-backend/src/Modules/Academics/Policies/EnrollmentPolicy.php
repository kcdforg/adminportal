<?php

declare(strict_types=1);

namespace App\Modules\Academics\Policies;

use App\Modules\Academics\Models\Enrollment;
use App\Modules\Families\Repositories\FamilyMemberRepository;

class EnrollmentPolicy
{
    public function __construct(private readonly FamilyMemberRepository $fmRepo) {}

    public function canList(array $jwt): bool
    {
        return $this->isAdmin($jwt) || $this->isFamilyMember($jwt);
    }

    public function canCreate(array $jwt, int $familyId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        return $this->isPrimaryMemberOfFamily($jwt, $familyId);
    }

    public function canView(array $jwt, Enrollment $enrollment): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return false;
        }

        if ($this->isPrimaryMemberOfFamily($jwt, $enrollment->family_id)) {
            return true;
        }

        return $enrollment->member_id === $profileId;
    }

    public function canCancel(array $jwt, Enrollment $enrollment): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        return $this->isPrimaryMemberOfFamily($jwt, $enrollment->family_id);
    }

    private function isPrimaryMemberOfFamily(array $jwt, int $familyId): bool
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return false;
        }

        $membership = $this->fmRepo->findByFamilyAndProfile($familyId, $profileId);
        return $membership !== null && $membership->member_role === 'primary';
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }

    private function isFamilyMember(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['family_primary', 'family_normal', 'family_student']
        ));
    }
}
