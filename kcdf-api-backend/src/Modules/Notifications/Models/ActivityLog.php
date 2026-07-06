<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    // activity_logs has created_at but no updated_at column (append-only)
    const UPDATED_AT = null;

    protected $fillable = [
        'actor_profile_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'created_at'  => 'datetime',
    ];
}
