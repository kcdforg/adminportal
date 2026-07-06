<?php

declare(strict_types=1);

namespace App\Modules\Community\Policies;

class InvitationPolicy
{
    public function canCreate(array $jwt): bool
    {
        return $this->isParent($jwt) || $this->isAnyAdmin($jwt);
    }

    public function canViewAll(array $jwt): bool
    {
        return $this->isAnyAdmin($jwt);
    }

    public function canCancel(array $jwt, int $invitedByMemberId): bool
    {
        if ($this->isAnyAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $profileId > 0 && $profileId === $invitedByMemberId;
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
