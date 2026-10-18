<?php

declare(strict_types=1);

namespace App\Modules\Payments\Repositories;

use App\Core\BaseRepository;
use App\Modules\Payments\Models\Payment;

class PaymentRepository extends BaseRepository
{
    protected string $modelClass = Payment::class;

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = Payment::query();

        if (!empty($filters['family_id'])) {
            $query->where('family_id', (int) $filters['family_id']);
        }
        if (!empty($filters['payment_type'])) {
            $query->where('payment_type', $filters['payment_type']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (!empty($filters['paid_at_from'])) {
            $query->where('paid_at', '>=', $filters['paid_at_from']);
        }
        if (!empty($filters['paid_at_to'])) {
            $query->where('paid_at', '<=', $filters['paid_at_to']);
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

    public function paginateForFamily(int $familyId, array $filters, int $perPage, int $page): array
    {
        $filters['family_id'] = $familyId;

        return $this->paginateFiltered($filters, $perPage, $page);
    }

    public function getNetPaidForEnrollment(int $enrollmentId): float
    {
        $totalPaid = Payment::where('enrollment_id', $enrollmentId)
            ->where('payment_type', '!=', 'refund')
            ->where('status', 'completed')
            ->sum('amount');

        $totalRefund = Payment::where('enrollment_id', $enrollmentId)
            ->where('payment_type', 'refund')
            ->where('status', 'completed')
            ->sum('amount');

        return (float) $totalPaid - (float) $totalRefund;
    }
}
