<?php

declare(strict_types=1);

namespace App\Modules\Payments\Policies;

use App\Modules\Families\Repositories\FamilyMemberRepository;
use App\Modules\Payments\Models\Payment;

class PaymentPolicy
{
    public function __construct(private readonly FamilyMemberRepository $fmRepo) {}

    public function canViewAll(array $jwt): bool
    {
        return $this->isAccountsAdmin($jwt);
    }

    public function canCreate(array $jwt): bool
    {
        return $this->isAccountsAdmin($jwt);
    }

    public function canView(array $jwt, Payment $payment): bool
    {
        if ($this->isAnyAdmin($jwt)) {
            return true;
        }

        return $this->isPrimaryMemberOfFamily($jwt, $payment->family_id);
    }

    public function canUpdate(array $jwt): bool
    {
        return $this->isAccountsAdmin($jwt);
    }

    public function canViewFamilyPayments(array $jwt, int $familyId): bool
    {
        if ($this->isAnyAdmin($jwt)) {
            return true;
        }

        return $this->isPrimaryMemberOfFamily($jwt, $familyId);
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

    private function isAccountsAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_accounts']
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
