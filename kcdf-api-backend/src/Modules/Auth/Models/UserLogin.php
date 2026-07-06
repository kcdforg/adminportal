<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class UserLogin extends Model
{
    protected $table = 'user_logins';

    protected $fillable = [
        'profile_id',
        'username',
        'password_hash',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = ['password_hash'];

    protected $casts = [
        'is_active'     => 'boolean',
        'last_login_at' => 'datetime',
    ];

    public function profile()
    {
        return $this->belongsTo(MemberProfile::class, 'profile_id');
    }
}
