<?php

declare(strict_types=1);

namespace App\Modules\Families\Repositories;

use App\Core\BaseRepository;
use App\Modules\Families\Models\Entity;
use App\Modules\Families\Models\EntityMemberRelation;
use Illuminate\Database\Eloquent\Collection;

class EntityRepository extends BaseRepository
{
    protected string $modelClass = Entity::class;

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Entity::query();

        if (!empty($filters['entity_type'])) {
            $query->where('entity_type', $filters['entity_type']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'name', 'entity_type', 'created_at'], true)
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

    public function getRelationsForMember(int $memberId): Collection
    {
        return EntityMemberRelation::with('entity')
            ->where('member_id', $memberId)
            ->orderBy('is_current', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function findRelation(int $relationId): ?EntityMemberRelation
    {
        return EntityMemberRelation::with('entity')->find($relationId);
    }

    public function createRelation(array $data): EntityMemberRelation
    {
        return EntityMemberRelation::create($data);
    }

    public function deleteRelation(EntityMemberRelation $relation): void
    {
        $relation->delete();
    }

    public function entityExists(int $entityId): bool
    {
        return Entity::where('id', $entityId)->exists();
    }
}
