<?php

declare(strict_types=1);

namespace App\Modules\Community\Repositories;

use App\Core\BaseRepository;
use App\Modules\Community\Models\Invitation;

class InvitationRepository extends BaseRepository
{
    protected string $modelClass = Invitation::class;

    public function findByCode(string $code): ?Invitation
    {
        return Invitation::where('invite_code', $code)->first();
    }

    public function paginateForSender(int $profileId, array $filters, int $perPage, int $page): array
    {
        $query = Invitation::where('invited_by_member_id', $profileId);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderBy('sent_at', 'desc');
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

    public function paginateAll(array $filters, int $perPage, int $page): array
    {
        $query = Invitation::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        $query->orderBy('sent_at', 'desc');
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

    public function hasActiveDuplicate(int $senderId, ?string $mobile, ?string $email): bool
    {
        $query = Invitation::where('invited_by_member_id', $senderId)
            ->where('status', 'pending');

        $query->where(function ($q) use ($mobile, $email) {
            if ($mobile !== null) {
                $q->orWhere('invite_mobile', $mobile);
            }
            if ($email !== null) {
                $q->orWhere('invite_email', $email);
            }
        });

        return $query->exists();
    }
}
