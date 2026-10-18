<?php

declare(strict_types=1);

namespace App\Modules\Community\Models;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $table = 'invitations';

    // invitations has sent_at / accepted_at but no created_at / updated_at columns
    public $timestamps = false;

    protected $fillable = [
        'invited_by_member_id',
        'invite_mobile',
        'invite_email',
        'invite_code',
        'status',
        'sent_at',
        'accepted_at',
    ];
}
