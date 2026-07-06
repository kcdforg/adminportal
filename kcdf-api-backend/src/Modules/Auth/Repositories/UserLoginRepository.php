<?php

declare(strict_types=1);

namespace App\Modules\Auth\Repositories;

use App\Core\BaseRepository;
use App\Modules\Auth\Models\UserLogin;

class UserLoginRepository extends BaseRepository
{
    protected string $modelClass = UserLogin::class;

    public function findByUsername(string $username): ?UserLogin
    {
        return UserLogin::where('username', $username)->first();
    }

    public function updateLastLogin(int $loginId): void
    {
        UserLogin::where('id', $loginId)->update(['last_login_at' => now()]);
    }
}
