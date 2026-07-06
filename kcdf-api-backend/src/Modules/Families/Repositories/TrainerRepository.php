<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\Trainer;

class TrainerRepository extends BaseRepository
{
    protected string $modelClass = Trainer::class;

    public function findByProfileId(int $profileId): ?Trainer
    {
        return Trainer::where('profile_id', $profileId)->first();
    }

    public function findWithRelations(int $id): ?Trainer
    {
        return Trainer::with(['profile', 'address'])->find($id);
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Trainer::with(['profile', 'address']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'trainer_code', 'created_at'], true)
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

    public static function generateTrainerCode(int $id): string
    {
        return 'TR-' . str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }
}
