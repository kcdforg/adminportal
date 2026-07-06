<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Repositories;

use App\Core\BaseRepository;
use App\Modules\Notifications\Models\ActivityLog;
use Illuminate\Database\Capsule\Manager as DB;

class ActivityLogRepository extends BaseRepository
{
    protected string $modelClass = ActivityLog::class;

    public function paginateFiltered(array $filters, int $perPage, int $page): array
    {
        $query = ActivityLog::leftJoin('member_profiles', 'activity_logs.actor_profile_id', '=', 'member_profiles.id')
            ->select(
                'activity_logs.*',
                DB::raw("CASE WHEN member_profiles.id IS NOT NULL
                    THEN CONCAT(member_profiles.first_name, ' ', member_profiles.last_name)
                    ELSE NULL END AS actor_name")
            );

        if (!empty($filters['actor_profile_id'])) {
            $query->where('activity_logs.actor_profile_id', (int) $filters['actor_profile_id']);
        }
        if (!empty($filters['entity_type'])) {
            $query->where('activity_logs.entity_type', $filters['entity_type']);
        }
        if (!empty($filters['entity_id'])) {
            $query->where('activity_logs.entity_id', (int) $filters['entity_id']);
        }
        if (!empty($filters['action'])) {
            $query->where('activity_logs.action', $filters['action']);
        }
        if (!empty($filters['created_at_from'])) {
            $query->where('activity_logs.created_at', '>=', $filters['created_at_from']);
        }
        if (!empty($filters['created_at_to'])) {
            $query->where('activity_logs.created_at', '<=', $filters['created_at_to']);
        }

        $query->orderBy('activity_logs.created_at', 'desc');
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
