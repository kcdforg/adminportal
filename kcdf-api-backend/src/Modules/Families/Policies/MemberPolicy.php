<?php

declare(strict_types=1);

namespace App\Modules\Families\Policies;

use App\Modules\Families\Repositories\FamilyMemberRepository;

class MemberPolicy
{
    public function __construct(private readonly FamilyMemberRepository $fmRepo) {}

    /**
     * Admin, the member themselves, or primary family member of the same family.
     */
    public function canView(array $jwt, int $profileId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }
        if ((int) ($jwt['profile_id'] ?? 0) === $profileId) {
            return true;
        }
        $requesterId = (int) ($jwt['profile_id'] ?? 0);
        return $requesterId > 0 && $this->fmRepo->isPrimaryMemberOfSameFamilyAs($requesterId, $profileId);
    }

    /**
     * Admin or the member themselves can edit.
     */
    public function canEdit(array $jwt, int $profileId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }
        return (int) ($jwt['profile_id'] ?? 0) === $profileId;
    }

    /**
     * Admin or primary family member of the member can manage entity relations.
     */
    public function canManageEntityRelations(array $jwt, int $profileId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }
        $requesterId = (int) ($jwt['profile_id'] ?? 0);
        return $requesterId > 0 && $this->fmRepo->isPrimaryMemberOfSameFamilyAs($requesterId, $profileId);
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }
}
