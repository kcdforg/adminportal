<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\BatchSession;

class SessionRepository extends BaseRepository
{
    protected string $modelClass = BatchSession::class;

    public function findWithRelations(int $id): ?BatchSession
    {
        return BatchSession::with(['batch.program', 'trainer.profile'])->find($id);
    }

    public function paginateForBatch(int $batchId, array $filters, int $perPage, int $page): array
    {
        $query = BatchSession::with(['trainer.profile'])
            ->where('batch_id', $batchId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['session_date_from'])) {
            $query->where('session_date', '>=', $filters['session_date_from']);
        }
        if (!empty($filters['session_date_to'])) {
            $query->where('session_date', '<=', $filters['session_date_to']);
        }

        $query->orderBy('session_date', 'asc')->orderBy('session_number', 'asc');

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
