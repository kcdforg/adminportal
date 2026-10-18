<?php

declare(strict_types=1);

namespace App\Modules\Community\Policies;

use App\Modules\Community\Repositories\GroupMemberRepository;

class GroupPolicy
{
    public function __construct(
        private readonly GroupMemberRepository $groupMemberRepo,
    ) {}

    public function canCreate(array $jwt): bool
    {
        return $this->isAnyAdmin($jwt);
    }

    public function canEdit(array $jwt): bool
    {
        return $this->isAnyAdmin($jwt);
    }

    public function canViewAll(array $jwt): bool
    {
        return $this->isAnyAdmin($jwt);
    }

    public function canView(array $jwt, int $groupId, string $visibility): bool
    {
        if ($this->isAnyAdmin($jwt)) {
            return true;
        }

        if ($visibility === 'public') {
            return $this->isParent($jwt);
        }

        // private or invite_only: must be an active member
        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $profileId > 0 && $this->groupMemberRepo->isMember($groupId, $profileId);
    }

    public function canViewMembers(array $jwt, int $groupId): bool
    {
        if ($this->isAnyAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $profileId > 0 && $this->groupMemberRepo->isMember($groupId, $profileId);
    }

    public function canJoin(array $jwt): bool
    {
        return $this->isParent($jwt);
    }

    public function canLeave(array $jwt): bool
    {
        return $this->isParent($jwt) || $this->isAnyAdmin($jwt);
    }

    public function canManageMember(array $jwt): bool
    {
        return $this->isAnyAdmin($jwt);
    }

    private function isParent(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['family_primary', 'family_normal']
        ));
    }

    private function isAnyAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }
}
