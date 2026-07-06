<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Repositories;

use App\Core\BaseRepository;
use App\Modules\Notifications\Models\Notification;

class NotificationRepository extends BaseRepository
{
    protected string $modelClass = Notification::class;

    public function paginateForMember(int $memberId, array $filters, int $perPage, int $page): array
    {
        $query = Notification::where('member_id', $memberId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        $query->orderBy('created_at', 'desc');
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ];
    }

    public function findByIdForMember(int $id, int $memberId): ?Notification
    {
        return Notification::where('id', $id)
            ->where('member_id', $memberId)
            ->first();
    }

    public function markAllReadForMember(int $memberId): int
    {
        return Notification::where('member_id', $memberId)
            ->where('status', 'unread')
            ->update(['status' => 'read', 'read_at' => now()]);
    }

    public function insertMany(array $records): void
    {
        Notification::insert($records);
    }
}
