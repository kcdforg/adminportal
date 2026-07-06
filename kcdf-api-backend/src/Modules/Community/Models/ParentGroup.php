<?php

declare(strict_types=1);

namespace App\Modules\Community\Models;

use Illuminate\Database\Eloquent\Model;

class ParentGroup extends Model
{
    protected $table = 'parent_groups';

    protected $fillable = [
        'group_name',
        'description',
        'visibility',
        'status',
    ];

    public function members()
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }
}
