<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Services;

use App\Core\Exceptions\UnauthorizedException;
use App\Modules\Notifications\Repositories\ActivityLogRepository;

/**
 * Read-only service for querying activity logs (admin view).
 * Write operations are handled by App\Core\ActivityLogService.
 */
class ActivityLogService
{
    public function __construct(
        private readonly ActivityLogRepository $activityLogRepo,
    ) {}

    public function list(array $filters, array $jwt): array
    {
        if (!$this->isSuperAdmin($jwt)) {
            throw new UnauthorizedException('Only super admins can view activity logs.');
        }

        $perPage = min((int) ($filters['per_page'] ?? 50), 200);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->activityLogRepo->paginateFiltered($filters, $perPage, $page);
    }

    private function isSuperAdmin(array $jwt): bool
    {
        return in_array('admin_super', $jwt['roles'] ?? [], true);
    }
}
