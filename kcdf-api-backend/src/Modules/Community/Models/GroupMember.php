<?php

declare(strict_types=1);

namespace App\Modules\Community\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $table = 'group_members';

    // group_members has no created_at / updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'group_id',
        'member_id',
        'joined_at',
        'status',
    ];

    public function group()
    {
        return $this->belongsTo(ParentGroup::class, 'group_id');
    }
}
