<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\Admin;

class AdminRepository extends BaseRepository
{
    protected string $modelClass = Admin::class;

    public function findByProfileId(int $profileId): ?Admin
    {
        return Admin::where('profile_id', $profileId)->first();
    }

    public function findWithProfile(int $id): ?Admin
    {
        return Admin::with('profile')->find($id);
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Admin::with('profile');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['admin_role'])) {
            $query->where('admin_role', $filters['admin_role']);
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'admin_role', 'created_at'], true)
            ? $filters['sort']
            : 'created_at';
        $order = strtolower($filters['order'] ?? 'desc') === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sort, $order);

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
