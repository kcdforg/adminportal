<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Auth\Models\MemberProfile;

class MemberRepository extends BaseRepository
{
    protected string $modelClass = MemberProfile::class;

    public function findByEmail(string $email): ?MemberProfile
    {
        return MemberProfile::where('email', $email)->first();
    }

    public function findByMobile(string $mobile): ?MemberProfile
    {
        return MemberProfile::where('mobile', $mobile)->first();
    }

    public function emailExistsExcept(string $email, int $excludeId): bool
    {
        return MemberProfile::where('email', $email)
            ->where('id', '!=', $excludeId)
            ->exists();
    }

    public function mobileExistsExcept(string $mobile, int $excludeId): bool
    {
        return MemberProfile::where('mobile', $mobile)
            ->where('id', '!=', $excludeId)
            ->exists();
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = MemberProfile::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', $search)
                  ->orWhere('last_name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('mobile', 'like', $search);
            });
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'first_name', 'last_name', 'created_at'], true)
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
