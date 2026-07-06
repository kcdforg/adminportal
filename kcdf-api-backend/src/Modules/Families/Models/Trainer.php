<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trainer extends Model
{
    protected $table = 'trainers';

    protected $fillable = [
        'profile_id',
        'trainer_code',
        'specialization',
        'experience_years',
        'bio',
        'joined_at',
        'address_id',
        'status',
    ];

    protected $casts = [
        'joined_at' => 'date',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'profile_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
}
