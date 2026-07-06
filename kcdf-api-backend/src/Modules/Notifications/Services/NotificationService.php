<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Notifications\DTOs\BroadcastNotificationDTO;
use App\Modules\Notifications\DTOs\SendNotificationDTO;
use App\Modules\Notifications\Models\Notification;
use App\Modules\Notifications\Repositories\NotificationRepository;
use App\Modules\Notifications\Validators\NotificationValidator;
use Illuminate\Database\Capsule\Manager as DB;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepository $notificationRepo,
        private readonly NotificationValidator  $validator,
        private readonly ActivityLogService     $activityLog,
    ) {}

    public function listForMember(array $filters, array $jwt): array
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);
        $perPage   = min((int) ($filters['per_page'] ?? 20), 100);
        $page      = max((int) ($filters['page'] ?? 1), 1);

        return $this->notificationRepo->paginateForMember($profileId, $filters, $perPage, $page);
    }

    public function markAsRead(int $id, array $jwt): Notification
    {
        $profileId    = (int) ($jwt['profile_id'] ?? 0);
        $notification = $this->notificationRepo->findByIdForMember($id, $profileId);

        if (!$notification) {
            throw new NotFoundException('Notification not found.');
        }

        if ($notification->status !== 'read') {
            $notification->update(['status' => 'read', 'read_at' => now()]);
        }

        return $notification->fresh();
    }

    public function markAllRead(array $jwt): int
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $this->notificationRepo->markAllReadForMember($profileId);
    }

    public function archive(int $id, array $jwt): Notification
    {
        $profileId    = (int) ($jwt['profile_id'] ?? 0);
        $notification = $this->notificationRepo->findByIdForMember($id, $profileId);

        if (!$notification) {
            throw new NotFoundException('Notification not found.');
        }

        if ($notification->status !== 'archived') {
            $notification->update(['status' => 'archived']);
        }

        return $notification->fresh();
    }

    public function send(array $data, array $jwt): int
    {
        if (!$this->isAdmin($jwt)) {
            throw new UnauthorizedException('Only admins can send notifications.');
        }

        $errors = $this->validator->validateSend($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto     = SendNotificationDTO::fromArray($data);
        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $now     = now();

        $records = array_map(fn (int $memberId) => [
            'member_id'  => $memberId,
            'title'      => $dto->title,
            'message'    => $dto->message,
            'type'       => $dto->type,
            'status'     => 'unread',
            'created_at' => $now,
        ], $dto->memberIds);

        DB::transaction(function () use ($records, $dto, $actorId) {
            $this->notificationRepo->insertMany($records);

            $this->activityLog->log(
                $actorId,
                'notification_sent',
                'notifications',
                0,
                null,
                [
                    'member_ids' => $dto->memberIds,
                    'title'      => $dto->title,
                    'type'       => $dto->type,
                ]
            );
        });

        return count($records);
    }

    public function broadcast(array $data, array $jwt): int
    {
        if (!$this->isAdmin($jwt)) {
            throw new UnauthorizedException('Only admins can broadcast notifications.');
        }

        $errors = $this->validator->validateBroadcast($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto       = BroadcastNotificationDTO::fromArray($data);
        $memberIds = $this->resolveBroadcastMemberIds($dto->targetType, $dto->targetId);

        if (empty($memberIds)) {
            return 0;
        }

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $now     = now();

        $records = array_map(fn (int $memberId) => [
            'member_id'  => $memberId,
            'title'      => $dto->title,
            'message'    => $dto->message,
            'type'       => $dto->type,
            'status'     => 'unread',
            'created_at' => $now,
        ], $memberIds);

        DB::transaction(function () use ($records, $dto, $actorId) {
            $this->notificationRepo->insertMany($records);

            $this->activityLog->log(
                $actorId,
                'notification_broadcast',
                'notifications',
                0,
                null,
                [
                    'target_type' => $dto->targetType,
                    'target_id'   => $dto->targetId,
                    'title'       => $dto->title,
                    'type'        => $dto->type,
                    'count'       => count($records),
                ]
            );
        });

        return count($records);
    }

    private function resolveBroadcastMemberIds(string $targetType, ?int $targetId): array
    {
        return match ($targetType) {
            'batch' => DB::table('batch_members')
                ->where('batch_id', $targetId)
                ->where('status', 'active')
                ->pluck('member_id')
                ->toArray(),

            'group' => DB::table('group_members')
                ->where('group_id', $targetId)
                ->where('status', 'active')
                ->pluck('member_id')
                ->toArray(),

            'all_families' => DB::table('family_members')
                ->pluck('profile_id')
                ->unique()
                ->values()
                ->toArray(),

            default => [],
        };
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }
}
