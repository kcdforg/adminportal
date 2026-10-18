<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use App\Modules\Families\Models\Trainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchSession extends Model
{
    protected $table = 'batch_sessions';

    protected $fillable = [
        'batch_id',
        'session_number',
        'session_title',
        'session_date',
        'start_time',
        'end_time',
        'session_type',
        'status',
        'trainer_id',
        'topics_covered',
        'homework',
        'notes',
        'attendance_locked',
    ];

    protected $casts = [
        'attendance_locked' => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'batch_session_id');
    }
}
