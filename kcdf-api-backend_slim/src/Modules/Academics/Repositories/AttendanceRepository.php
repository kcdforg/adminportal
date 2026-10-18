<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\Attendance;

class AttendanceRepository extends BaseRepository
{
    protected string $modelClass = Attendance::class;

    public function findBySessionAndMember(int $sessionId, int $memberId): ?Attendance
    {
        return Attendance::where('batch_session_id', $sessionId)
            ->where('member_id', $memberId)
            ->first();
    }

    public function getForSession(int $sessionId): array
    {
        return Attendance::with('member')
            ->where('batch_session_id', $sessionId)
            ->get()
            ->toArray();
    }

    public function upsert(int $sessionId, int $memberId, array $data): Attendance
    {
        $record = $this->findBySessionAndMember($sessionId, $memberId);

        if ($record) {
            $record->update($data);
            return $record->fresh();
        }

        return Attendance::create(array_merge($data, [
            'batch_session_id' => $sessionId,
            'member_id'        => $memberId,
        ]));
    }
}
