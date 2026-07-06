<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $table = 'programs';

    protected $fillable = [
        'program_name',
        'program_type',
        'description',
        'age_group',
        'fee_amount',
        'status',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(StudentBatch::class, 'program_id');
    }
}
