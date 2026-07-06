<?php

declare(strict_types=1);

namespace App\Modules\Payments\Models;

use App\Modules\Academics\Models\Enrollment;
use App\Modules\Families\Models\Family;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $table = 'payments';

    protected $fillable = [
        'family_id',
        'enrollment_id',
        'payment_type',
        'amount',
        'payment_method',
        'transaction_reference',
        'status',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    protected $hidden = ['updated_at'];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }
}
