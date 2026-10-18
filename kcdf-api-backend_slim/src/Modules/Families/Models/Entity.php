<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entity extends Model
{
    protected $table = 'entities';

    protected $fillable = [
        'entity_type',
        'name',
        'city',
        'state',
        'country',
        'meta',
        'status',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function memberRelations(): HasMany
    {
        return $this->hasMany(EntityMemberRelation::class, 'entity_id');
    }
}
