<?php

declare(strict_types=1);

namespace App\Modules\Community\Repositories;

use App\Core\BaseRepository;
use App\Modules\Community\Models\GroupMember;
use Illuminate\Database\Capsule\Manager as DB;

class GroupMemberRepository extends BaseRepository
{
    protected string $modelClass = GroupMember::class;

    public function findByGroupAndMember(int $groupId, int $memberId): ?GroupMember
    {
        return GroupMember::where('group_id', $groupId)
            ->where('member_id', $memberId)
            ->first();
    }

    public function getActiveMembersForGroup(int $groupId): array
    {
        return GroupMember::select('group_members.*', DB::raw("CONCAT(mp.first_name, ' ', mp.last_name) AS member_name"), 'mp.mobile', 'mp.email')
            ->join('member_profiles AS mp', 'group_members.member_id', '=', 'mp.id')
            ->where('group_members.group_id', $groupId)
            ->where('group_members.status', 'active')
            ->get()
            ->toArray();
    }

    public function getActiveMemberIdsForGroup(int $groupId): array
    {
        return GroupMember::where('group_id', $groupId)
            ->where('status', 'active')
            ->pluck('member_id')
            ->toArray();
    }

    public function isMember(int $groupId, int $memberId): bool
    {
        return GroupMember::where('group_id', $groupId)
            ->where('member_id', $memberId)
            ->where('status', 'active')
            ->exists();
    }
}
