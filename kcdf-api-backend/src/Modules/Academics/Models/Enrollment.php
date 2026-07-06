<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use App\Modules\Auth\Models\MemberProfile;
use App\Modules\Families\Models\Family;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'enrollments';

    protected $fillable = [
        'family_id',
        'member_id',
        'batch_id',
        'enrolled_by_member_id',
        'enrolled_at',
        'status',
        'payment_status',
        'fee_amount',
    ];

    protected $casts = [
        'fee_amount'  => 'decimal:2',
        'enrolled_at' => 'datetime',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id');
    }

    public function enrolledBy(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'enrolled_by_member_id');
    }
}
