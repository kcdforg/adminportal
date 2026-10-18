<?php

declare(strict_types=1);

namespace App\Modules\Auth\Services;

use App\Modules\Auth\Repositories\ProfileRepository;
use App\Modules\Auth\Repositories\UserLoginRepository;
use App\Core\SystemClock;
use DateInterval;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager as DB;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;

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

        $this->loginRepository->updateLastLogin($login->id);

        return $this->buildAuthResponse($profile->id, $login->username, $roleData, $profile);
    }

    public function refresh(string $refreshToken): array
    {
        $jwtConfig = $this->jwtConfiguration();

        try {
            $token = $jwtConfig->parser()->parse($refreshToken);
            $constraints = [
                new SignedWith($jwtConfig->signer(), $jwtConfig->signingKey()),
                new StrictValidAt(new SystemClock(), new DateInterval('PT60S')),
            ];

            if (!$jwtConfig->validator()->validate($token, ...$constraints)) {
                throw new \RuntimeException('Invalid or expired refresh token', 401);
            }

            if ($token->claims()->get('type') !== 'refresh') {
                throw new \RuntimeException('Invalid or expired refresh token', 401);
            }
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Throwable) {
            throw new \RuntimeException('Invalid or expired refresh token', 401);
        }

        $tokenHash = hash('sha256', $refreshToken);
        $claims = $token->claims();
        $profileId = (int) $claims->get('sub');

        return DB::transaction(function () use ($tokenHash, $profileId) {
            $stored = DB::table('refresh_tokens')
                ->where('token_hash', $tokenHash)
                ->whereNull('revoked_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (!$stored) {
                throw new \RuntimeException('Refresh token has been revoked or expired', 401);
            }

            DB::table('refresh_tokens')->where('id', $stored->id)->update(['revoked_at' => now()]);

            $login = DB::table('user_logins')->where('profile_id', $profileId)->first();
            if (!$login) {
                throw new \RuntimeException('Invalid or expired refresh token', 401);
            }

            $roleData = $this->profileRepository->getRolesForProfile($profileId);
            $profile = $this->profileRepository->findOrFail($profileId);

            return $this->buildAuthResponse($profileId, $login->username, $roleData, $profile);
        });
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

    /**
     * Issue a fresh access + refresh token pair (used by login, refresh, invitation accept).
     */
    public function issueTokenPair(int $profileId, string $username, array $roleData): array
    {
        return [
            'access_token'  => $this->issueAccessToken($profileId, $username, $roleData),
            'refresh_token' => $this->issueRefreshToken($profileId),
            'token_type'    => 'Bearer',
            'expires_in'    => $this->config['jwt']['access_ttl'],
        ];
    }

    private function buildAuthResponse(int $profileId, string $username, array $roleData, object $profile): array
    {
        $tokens = $this->issueTokenPair($profileId, $username, $roleData);

        return array_merge($tokens, [
            'profile' => [
                'id'         => $profile->id,
                'first_name' => $profile->first_name,
                'last_name'  => $profile->last_name,
                'roles'      => $roleData['roles'],
                'family_ids' => $roleData['family_ids'],
            ],
        ]);
    }

    private function issueAccessToken(int $profileId, string $username, array $roleData): string
    {
        $jwtConfig = $this->jwtConfiguration();
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+' . $this->config['jwt']['access_ttl'] . ' seconds');

        $token = $jwtConfig->builder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->relatedTo((string) $profileId)
            ->withClaim('profile_id', $profileId)
            ->withClaim('username', $username)
            ->withClaim('roles', $roleData['roles'])
            ->withClaim('family_ids', $roleData['family_ids'])
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());

        return $token->toString();
    }

    private function issueRefreshToken(int $profileId): string
    {
        $jwtConfig = $this->jwtConfiguration();
        $now = new DateTimeImmutable();
        $expiresAt = $now->modify('+' . $this->config['jwt']['refresh_ttl'] . ' seconds');

        $token = $jwtConfig->builder()
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($expiresAt)
            ->relatedTo((string) $profileId)
            ->withClaim('type', 'refresh')
            ->getToken($jwtConfig->signer(), $jwtConfig->signingKey());

        $tokenString = $token->toString();
        $tokenHash = hash('sha256', $tokenString);

        DB::table('refresh_tokens')->insert([
            'profile_id' => $profileId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
            'created_at' => now(),
        ]);

        return $tokenString;
    }

    private function jwtConfiguration(): Configuration
    {
        $secret = $this->config['jwt']['secret'] ?? null;
        if (empty($secret)) {
            throw new \RuntimeException('JWT secret is not configured', 401);
        }

        return Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText($secret)
        );
    }
}
