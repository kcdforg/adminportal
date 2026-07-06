<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\StudentBatch;
use Illuminate\Database\Capsule\Manager as DB;

class BatchRepository extends BaseRepository
{
    protected string $modelClass = StudentBatch::class;

    public function findWithRelations(int $id): ?StudentBatch
    {
        return StudentBatch::with(['program', 'trainer.profile'])->find($id);
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = StudentBatch::with(['program', 'trainer.profile']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['program_id'])) {
            $query->where('program_id', (int) $filters['program_id']);
        }
        if (!empty($filters['trainer_id'])) {
            $query->where('trainer_id', (int) $filters['trainer_id']);
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'batch_name', 'start_date', 'created_at'], true)
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

    public function paginateForTrainer(int $trainerId, array $filters, int $perPage, int $page): array
    {
        $query = StudentBatch::with(['program', 'trainer.profile'])
            ->where('trainer_id', $trainerId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['program_id'])) {
            $query->where('program_id', (int) $filters['program_id']);
        }

        $paginator = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

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

    public function paginateForMember(int $profileId, array $filters, int $perPage, int $page): array
    {
        $enrolledBatchIds = DB::table('enrollments')
            ->where('member_id', $profileId)
            ->whereIn('status', ['active', 'pending'])
            ->pluck('batch_id')
            ->toArray();

        $query = StudentBatch::with(['program', 'trainer.profile'])
            ->whereIn('id', $enrolledBatchIds);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['program_id'])) {
            $query->where('program_id', (int) $filters['program_id']);
        }

        $paginator = $query->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

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

    public function countActiveMembers(int $batchId): int
    {
        return DB::table('batch_members')
            ->where('batch_id', $batchId)
            ->where('status', 'active')
            ->count();
    }
}
