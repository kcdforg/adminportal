<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\BatchMember;

class BatchMemberRepository extends BaseRepository
{
    protected string $modelClass = BatchMember::class;

    public function findByBatchAndMember(int $batchId, int $memberId): ?BatchMember
    {
        return BatchMember::where('batch_id', $batchId)
            ->where('member_id', $memberId)
            ->first();
    }

    public function getMembersForBatch(int $batchId): array
    {
        $members = BatchMember::with('member')
            ->where('batch_id', $batchId)
            ->get();

        return $members->toArray();
    }

    public function getMemberIdsForBatch(int $batchId): array
    {
        return BatchMember::where('batch_id', $batchId)
            ->where('status', 'active')
            ->pluck('member_id')
            ->toArray();
    }

    public function isMemberInBatch(int $batchId, int $memberId): bool
    {
        return BatchMember::where('batch_id', $batchId)
            ->where('member_id', $memberId)
            ->exists();
    }
}
