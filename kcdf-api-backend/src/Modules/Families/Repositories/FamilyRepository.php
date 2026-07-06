<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\Family;

class FamilyRepository extends BaseRepository
{
    protected string $modelClass = Family::class;

    public function findWithAddress(int $id): ?Family
    {
        return Family::with('address')->find($id);
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Family::with('address');

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where('family_name', 'like', '%' . $filters['search'] . '%');
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'family_name', 'family_code', 'created_at'], true)
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

    public static function generateFamilyCode(int $id): string
    {
        return 'KCDF-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }
}
