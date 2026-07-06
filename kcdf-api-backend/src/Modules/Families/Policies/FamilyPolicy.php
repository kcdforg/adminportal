<?php

declare(strict_types=1);

namespace App\Modules\Families\Policies;

use App\Modules\Families\Repositories\FamilyMemberRepository;

class FamilyPolicy
{
    public function __construct(private readonly FamilyMemberRepository $fmRepo) {}

    /**
     * Admin, primary member, or normal member can view a family.
     * Students cannot.
     */
    public function canView(array $jwt, int $familyId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }
        $membership = $this->getMembership($jwt, $familyId);
        return $membership !== null && $membership->member_role !== 'student';
    }

    /**
     * Elevated admin (super/pm) or primary member can edit a family.
     */
    public function canEdit(array $jwt, int $familyId): bool
    {
        if ($this->isElevatedAdmin($jwt)) {
            return true;
        }
        $membership = $this->getMembership($jwt, $familyId);
        return $membership !== null && $membership->member_role === 'primary';
    }

    /**
     * Elevated admin or primary member can manage (add/remove) family members.
     */
    public function canManageMembers(array $jwt, int $familyId): bool
    {
        return $this->canEdit($jwt, $familyId);
    }

    /**
     * Admin, primary member, or normal member can view the member list.
     * Students cannot.
     */
    public function canViewMembers(array $jwt, int $familyId): bool
    {
        return $this->canView($jwt, $familyId);
    }

    private function getMembership(array $jwt, int $familyId): ?object
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return null;
        }
        return $this->fmRepo->findByFamilyAndProfile($familyId, $profileId);
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }

    private function isElevatedAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager']
        ));
    }
}
