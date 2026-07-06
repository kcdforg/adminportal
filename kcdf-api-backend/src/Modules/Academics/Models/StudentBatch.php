<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use App\Modules\Families\Models\Trainer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentBatch extends Model
{
    protected $table = 'student_batches';

    protected $fillable = [
        'program_id',
        'batch_name',
        'capacity',
        'trainer_id',
        'start_date',
        'end_date',
        'status',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(BatchMember::class, 'batch_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(BatchSession::class, 'batch_id');
    }
}
