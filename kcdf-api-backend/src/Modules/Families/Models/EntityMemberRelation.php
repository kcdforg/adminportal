<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use App\Modules\Auth\Models\MemberProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EntityMemberRelation extends Model
{
    protected $table = 'entity_member_relations';

    protected $fillable = [
        'member_id',
        'entity_id',
        'relation_type',
        'start_date',
        'end_date',
        'is_current',
        'relation_context',
    ];

    protected $casts = [
        'start_date'       => 'date',
        'end_date'         => 'date',
        'is_current'       => 'boolean',
        'relation_context' => 'array',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(MemberProfile::class, 'member_id');
    }

    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }
}
