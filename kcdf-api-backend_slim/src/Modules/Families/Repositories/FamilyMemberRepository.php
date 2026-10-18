<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\FamilyMember;
use Illuminate\Database\Eloquent\Collection;

class FamilyMemberRepository extends BaseRepository
{
    protected string $modelClass = FamilyMember::class;

    public function findByFamilyAndProfile(int $familyId, int $profileId, bool $activeOnly = true): ?FamilyMember
    {
        $query = FamilyMember::where('family_id', $familyId)
            ->where('profile_id', $profileId);

        if ($activeOnly) {
            $query->active();
        }

        return $query->first();
    }

    public function findPrimaryMember(int $familyId): ?FamilyMember
    {
        return FamilyMember::active()
            ->where('family_id', $familyId)
            ->where('member_role', 'primary')
            ->first();
    }

    public function getMembersForFamily(int $familyId): Collection
    {
        return FamilyMember::with('profile')
            ->active()
            ->where('family_id', $familyId)
            ->get();
    }

    public function isPrimaryMemberOfSameFamilyAs(int $requesterProfileId, int $targetProfileId): bool
    {
        $primaryFamilyIds = FamilyMember::active()
            ->where('profile_id', $requesterProfileId)
            ->where('member_role', 'primary')
            ->pluck('family_id')
            ->toArray();

        if (empty($primaryFamilyIds)) {
            return false;
        }

        return FamilyMember::active()
            ->whereIn('family_id', $primaryFamilyIds)
            ->where('profile_id', $targetProfileId)
            ->exists();
    }

    public function getFamilyIdsForProfile(int $profileId): array
    {
        return FamilyMember::active()
            ->where('profile_id', $profileId)
            ->pluck('family_id')
            ->toArray();
    }
}
