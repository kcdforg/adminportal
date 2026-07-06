<?php

declare(strict_types=1);

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class MemberProfile extends Model
{
    protected $table = 'member_profiles';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'gender',
        'mobile',
        'email',
        'photo_url',
        'blood_group',
        'status',
    ];

    protected $hidden = [];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function userLogin()
    {
        return $this->hasOne(UserLogin::class, 'profile_id');
    }
}
