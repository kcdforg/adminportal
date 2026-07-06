<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\ProfileRepository;
use App\Modules\Auth\Repositories\UserLoginRepository;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Capsule\Manager as DB;

class AuthService
{
    public function __construct(
        private readonly UserLoginRepository $loginRepository,
        private readonly ProfileRepository $profileRepository,
        private readonly array $config
    ) {}

    public function login(string $username, string $password): array
    {
        $login = $this->loginRepository->findByUsername($username);

        if (!$login || !password_verify($password, $login->password_hash)) {
            throw new \RuntimeException('Invalid credentials', 401);
        }

        if (!$login->is_active) {
            throw new \RuntimeException('Account is deactivated', 401);
        }

        $profile = $this->profileRepository->findOrFail($login->profile_id);
        $roleData = $this->profileRepository->getRolesForProfile($login->profile_id);

        $accessToken  = $this->issueAccessToken($profile->id, $login->username, $roleData);
        $refreshToken = $this->issueRefreshToken($profile->id);

        $this->loginRepository->updateLastLogin($login->id);

        return [
            'access_token'  => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => $this->config['jwt']['access_ttl'],
            'profile'       => [
                'id'         => $profile->id,
                'first_name' => $profile->first_name,
                'last_name'  => $profile->last_name,
                'roles'      => $roleData['roles'],
                'family_ids' => $roleData['family_ids'],
            ],
        ];
    }

    public function refresh(string $refreshToken): array
    {
        try {
            $decoded = JWT::decode($refreshToken, new Key($this->config['jwt']['secret'], 'HS256'));
        } catch (\Throwable) {
            throw new \RuntimeException('Invalid or expired refresh token', 401);
        }

        $tokenHash = hash('sha256', $refreshToken);
        $stored = DB::table('refresh_tokens')
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();

        if (!$stored) {
            throw new \RuntimeException('Refresh token has been revoked or expired', 401);
        }

        // Revoke old token
        DB::table('refresh_tokens')->where('id', $stored->id)->update(['revoked_at' => now()]);

        $profileId = (int) $decoded->sub;
        $login = DB::table('user_logins')->where('profile_id', $profileId)->first();
        $roleData = $this->profileRepository->getRolesForProfile($profileId);

        $newAccessToken  = $this->issueAccessToken($profileId, $login->username, $roleData);
        $newRefreshToken = $this->issueRefreshToken($profileId);

        $profile = $this->profileRepository->findOrFail($profileId);

        return [
            'access_token'  => $newAccessToken,
            'refresh_token' => $newRefreshToken,
            'token_type'    => 'Bearer',
            'expires_in'    => $this->config['jwt']['access_ttl'],
            'profile'       => [
                'id'         => $profile->id,
                'first_name' => $profile->first_name,
                'last_name'  => $profile->last_name,
                'roles'      => $roleData['roles'],
                'family_ids' => $roleData['family_ids'],
            ],
        ];
    }

    public function logout(int $profileId, string $refreshToken): void
    {
        $tokenHash = hash('sha256', $refreshToken);
        DB::table('refresh_tokens')
            ->where('profile_id', $profileId)
            ->where('token_hash', $tokenHash)
            ->update(['revoked_at' => now()]);
    }

    public function getProfile(int $profileId): array
    {
        $profile  = $this->profileRepository->findOrFail($profileId);
        $roleData = $this->profileRepository->getRolesForProfile($profileId);

        return array_merge($profile->toArray(), $roleData);
    }

    private function issueAccessToken(int $profileId, string $username, array $roleData): string
    {
        $now = time();
        $payload = [
            'sub'        => $profileId,
            'profile_id' => $profileId,
            'username'   => $username,
            'roles'      => $roleData['roles'],
            'family_ids' => $roleData['family_ids'],
            'iat'        => $now,
            'exp'        => $now + $this->config['jwt']['access_ttl'],
        ];

        return JWT::encode($payload, $this->config['jwt']['secret'], 'HS256');
    }

    private function issueRefreshToken(int $profileId): string
    {
        $now = time();
        $payload = [
            'sub'  => $profileId,
            'type' => 'refresh',
            'iat'  => $now,
            'exp'  => $now + $this->config['jwt']['refresh_ttl'],
        ];

        $token     = JWT::encode($payload, $this->config['jwt']['secret'], 'HS256');
        $tokenHash = hash('sha256', $token);

        DB::table('refresh_tokens')->insert([
            'profile_id' => $profileId,
            'token_hash' => $tokenHash,
            'expires_at' => date('Y-m-d H:i:s', $now + $this->config['jwt']['refresh_ttl']),
            'created_at' => now(),
        ]);

        return $token;
    }
}
