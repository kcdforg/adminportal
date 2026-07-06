<?php

declare(strict_types=1);

namespace App\Modules\Families\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    protected $table = 'families';

    protected $fillable = [
        'family_code',
        'family_name',
        'address_id',
        'status',
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class, 'family_id');
    }
}
