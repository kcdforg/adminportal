<?php

declare(strict_types=1);

namespace App\Modules\Community\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Auth\Repositories\ProfileRepository;
use App\Modules\Community\DTOs\CreateInvitationDTO;
use App\Modules\Community\Models\Invitation;
use App\Modules\Community\Policies\InvitationPolicy;
use App\Modules\Community\Repositories\InvitationRepository;
use App\Modules\Community\Validators\InvitationValidator;
use Firebase\JWT\JWT;
use Illuminate\Database\Capsule\Manager as DB;

class InvitationService
{
    public function __construct(
        private readonly InvitationRepository $invitationRepo,
        private readonly ProfileRepository    $profileRepo,
        private readonly InvitationPolicy     $policy,
        private readonly InvitationValidator  $validator,
        private readonly ActivityLogService   $activityLog,
        private readonly array                $config,
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        if ($this->policy->canViewAll($jwt)) {
            return $this->invitationRepo->paginateAll($filters, $perPage, $page);
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $this->invitationRepo->paginateForSender($profileId, $filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Invitation
    {
        if (!$this->policy->canCreate($jwt)) {
            throw new UnauthorizedException('You do not have permission to send invitations.');
        }

        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto       = CreateInvitationDTO::fromArray($data);
        $profileId = (int) ($jwt['profile_id'] ?? 0);

        if ($this->invitationRepo->hasActiveDuplicate($profileId, $dto->inviteMobile, $dto->inviteEmail)) {
            throw new DuplicateException('An active invitation has already been sent to this contact.');
        }

        $inviteCode = $this->generateInviteCode();

        return DB::transaction(function () use ($dto, $profileId, $inviteCode) {
            $invitation = Invitation::create([
                'invited_by_member_id' => $profileId,
                'invite_mobile'        => $dto->inviteMobile,
                'invite_email'         => $dto->inviteEmail,
                'invite_code'          => $inviteCode,
                'status'               => 'pending',
                'sent_at'              => now(),
            ]);

            $this->activityLog->log(
                $profileId,
                'created',
                'invitations',
                $invitation->id,
                null,
                [
                    'invite_mobile' => $dto->inviteMobile,
                    'invite_email'  => $dto->inviteEmail,
                    'invite_code'   => $inviteCode,
                ]
            );

            return $invitation->fresh();
        });
    }

    public function showByCode(string $code): array
    {
        $invitation = $this->invitationRepo->findByCode($code);
        if (!$invitation) {
            throw new NotFoundException('Invitation not found.');
        }

        if ($invitation->status === 'accepted') {
            throw new BusinessRuleException('INVITATION_ALREADY_ACCEPTED', 'This invitation has already been accepted.');
        }

        if ($invitation->status === 'cancelled') {
            throw new BusinessRuleException('INVITATION_CANCELLED', 'This invitation has been cancelled.');
        }

        if ($invitation->status === 'expired' || $this->isExpired($invitation)) {
            if ($invitation->status !== 'expired') {
                $invitation->update(['status' => 'expired']);
            }
            throw new BusinessRuleException('INVALID_INVITE_CODE', 'This invitation has expired.');
        }

        $sentAt    = strtotime($invitation->sent_at);
        $expiresAt = date('Y-m-d\TH:i:s\Z', $sentAt + (7 * 24 * 3600));

        $inviterName = 'Unknown';
        $inviter     = $this->profileRepo->findById($invitation->invited_by_member_id);
        if ($inviter) {
            $inviterName = trim($inviter->first_name . ' ' . $inviter->last_name);
        }

        return [
            'invite_code' => $invitation->invite_code,
            'invited_by'  => $inviterName,
            'status'      => $invitation->status,
            'expires_at'  => $expiresAt,
        ];
    }

    public function accept(string $code, array $data): array
    {
        $invitation = $this->invitationRepo->findByCode($code);
        if (!$invitation) {
            throw new NotFoundException('Invitation not found.');
        }

        if ($invitation->status !== 'pending') {
            throw new BusinessRuleException('INVITATION_NOT_PENDING', 'This invitation cannot be accepted.');
        }

        if ($this->isExpired($invitation)) {
            $invitation->update(['status' => 'expired']);
            throw new BusinessRuleException('INVALID_INVITE_CODE', 'This invitation has expired.');
        }

        $errors = $this->validator->validateAccept($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $email    = (string) $data['email'];
        $mobile   = (string) $data['mobile'];
        $password = (string) $data['password'];

        $existingLogin = DB::table('user_logins')->where('username', $email)->first();
        if ($existingLogin) {
            throw new DuplicateException('An account with this email already exists.');
        }

        return DB::transaction(function () use ($invitation, $data, $email, $mobile, $password) {
            $profile = DB::table('member_profiles')->insertGetId([
                'first_name' => (string) $data['first_name'],
                'last_name'  => (string) $data['last_name'],
                'mobile'     => $mobile,
                'email'      => $email,
                'status'     => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('user_logins')->insert([
                'profile_id'    => $profile,
                'username'      => $email,
                'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                'is_active'     => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            $invitation->update([
                'status'      => 'accepted',
                'accepted_at' => now(),
            ]);

            $this->activityLog->log(
                $profile,
                'invitation_accepted',
                'invitations',
                $invitation->id,
                ['status' => 'pending'],
                ['status' => 'accepted', 'new_profile_id' => $profile]
            );

            $roleData     = $this->profileRepo->getRolesForProfile($profile);
            $accessToken  = $this->issueAccessToken($profile, $email, $roleData);
            $refreshToken = $this->issueRefreshToken($profile);

            return [
                'access_token'  => $accessToken,
                'refresh_token' => $refreshToken,
            ];
        });
    }

    public function cancel(int $id, array $jwt): void
    {
        $invitation = $this->invitationRepo->findById($id);
        if (!$invitation) {
            throw new NotFoundException('Invitation not found.');
        }

        if (!$this->policy->canCancel($jwt, (int) $invitation->invited_by_member_id)) {
            throw new UnauthorizedException('You do not have permission to cancel this invitation.');
        }

        if ($invitation->status !== 'pending') {
            throw new BusinessRuleException('INVITATION_NOT_PENDING', 'Only pending invitations can be cancelled.');
        }

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;

        DB::transaction(function () use ($invitation, $actorId) {
            $invitation->update(['status' => 'cancelled']);

            $this->activityLog->log(
                $actorId,
                'status_changed',
                'invitations',
                $invitation->id,
                ['status' => 'pending'],
                ['status' => 'cancelled']
            );
        });
    }

    private function isExpired(Invitation $invitation): bool
    {
        $sentAt    = strtotime((string) $invitation->sent_at);
        $expiresAt = $sentAt + (7 * 24 * 3600);

        return time() > $expiresAt;
    }

    private function generateInviteCode(): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code  = '';
        for ($i = 0; $i < 12; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // Ensure uniqueness — retry on collision
        if (DB::table('invitations')->where('invite_code', $code)->exists()) {
            return $this->generateInviteCode();
        }

        return $code;
    }

    private function issueAccessToken(int $profileId, string $username, array $roleData): string
    {
        $now     = time();
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
        $now     = time();
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
