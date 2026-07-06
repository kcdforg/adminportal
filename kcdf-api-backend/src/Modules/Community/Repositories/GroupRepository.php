<?php

declare(strict_types=1);

namespace App\Modules\Community\Repositories;

use App\Core\BaseRepository;
use App\Modules\Community\Models\ParentGroup;

class GroupRepository extends BaseRepository
{
    protected string $modelClass = ParentGroup::class;

    public function paginateForAdmin(array $filters, int $perPage, int $page): array
    {
        $query = ParentGroup::query();

        if (!empty($filters['visibility'])) {
            $query->where('visibility', $filters['visibility']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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

    public function paginateForParent(int $profileId, array $filters, int $perPage, int $page): array
    {
        $query = ParentGroup::where(function ($q) use ($profileId) {
            $q->where('visibility', 'public')
              ->orWhereHas('members', function ($mq) use ($profileId) {
                  $mq->where('member_id', $profileId)->where('status', 'active');
              });
        });

        if (!empty($filters['visibility'])) {
            $query->where('visibility', $filters['visibility']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
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
}
