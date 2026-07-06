<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    // notifications has created_at but no updated_at column
    const UPDATED_AT = null;

    protected $fillable = [
        'member_id',
        'title',
        'message',
        'type',
        'status',
        'read_at',
    ];

    protected $casts = [
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
    ];
}
