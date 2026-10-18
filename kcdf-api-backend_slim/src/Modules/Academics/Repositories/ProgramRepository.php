<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\Program;

class ProgramRepository extends BaseRepository
{
    protected string $modelClass = Program::class;

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Program::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['program_type'])) {
            $query->where('program_type', $filters['program_type']);
        }

        $sort  = in_array($filters['sort'] ?? '', ['id', 'program_name', 'program_type', 'created_at'], true)
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
