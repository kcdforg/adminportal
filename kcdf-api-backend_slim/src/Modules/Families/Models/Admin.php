<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'profile_id',
        'admin_role',
        'status',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'profile_id');
    }
}
