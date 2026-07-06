<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatchMember extends Model
{
    protected $table = 'batch_members';

    protected $fillable = [
        'batch_id',
        'member_id',
        'joined_at',
        'status',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(StudentBatch::class, 'batch_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }
}
