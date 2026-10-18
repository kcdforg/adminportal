<?php

declare(strict_types=1);

namespace App\Modules\Academics\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $table = 'attendance';

    protected $fillable = [
        'batch_session_id',
        'member_id',
        'attendance_status',
        'remarks',
        'marked_by_member_id',
        'marked_at',
    ];

    protected $casts = [
        'marked_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(BatchSession::class, 'batch_session_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'marked_by_member_id');
    }
}
