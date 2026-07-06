<?php

declare(strict_types=1);

namespace App\Core;

use Illuminate\Database\Capsule\Manager as DB;

class ActivityLogService
{
    public function log(
        ?int $actorProfileId,
        string $action,
        string $entityType,
        int $entityId,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        DB::table('activity_logs')->insert([
            'actor_profile_id' => $actorProfileId,
            'action'           => $action,
            'entity_type'      => $entityType,
            'entity_id'        => $entityId,
            'old_values'       => $oldValues !== null ? json_encode($oldValues) : null,
            'new_values'       => $newValues !== null ? json_encode($newValues) : null,
            'created_at'       => now(),
        ]);
    }
}
