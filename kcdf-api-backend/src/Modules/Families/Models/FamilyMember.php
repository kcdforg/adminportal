<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    protected $table = 'family_members';

    protected $fillable = [
        'family_id',
        'profile_id',
        'relationship_type',
        'member_role',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'family_id');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'profile_id');
    }
}
