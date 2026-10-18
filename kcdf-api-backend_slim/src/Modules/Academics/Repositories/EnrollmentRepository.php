<?php

declare(strict_types=1);

namespace App\Modules\Academics\Repositories;

use App\Core\BaseRepository;
use App\Modules\Academics\Models\Enrollment;

class EnrollmentRepository extends BaseRepository
{
    protected string $modelClass = Enrollment::class;

    public function findWithRelations(int $id): ?Enrollment
    {
        return Enrollment::with(['family', 'member', 'batch.program', 'enrolledBy'])->find($id);
    }

    public function findByMemberAndBatch(int $memberId, int $batchId): ?Enrollment
    {
        return Enrollment::where('member_id', $memberId)
            ->where('batch_id', $batchId)
            ->first();
    }

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Enrollment::with(['family', 'member', 'batch.program']);

        if (!empty($filters['family_id'])) {
            $query->where('family_id', (int) $filters['family_id']);
        }
        if (!empty($filters['batch_id'])) {
            $query->where('batch_id', (int) $filters['batch_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        $query->orderBy('enrolled_at', 'desc');

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

    public function paginateForFamily(int $familyId, array $filters, int $perPage, int $page): array
    {
        $filters['family_id'] = $familyId;
        return $this->paginateFiltered($filters, $perPage, $page);
    }
}
