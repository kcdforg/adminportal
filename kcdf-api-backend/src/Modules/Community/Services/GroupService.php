<?php

declare(strict_types=1);

namespace App\Modules\Community\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\DuplicateException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Community\DTOs\CreateGroupDTO;
use App\Modules\Community\Models\GroupMember;
use App\Modules\Community\Models\ParentGroup;
use App\Modules\Community\Policies\GroupPolicy;
use App\Modules\Community\Repositories\GroupMemberRepository;
use App\Modules\Community\Repositories\GroupRepository;
use App\Modules\Community\Validators\GroupValidator;
use Illuminate\Database\Capsule\Manager as DB;

class GroupService
{
    public function __construct(
        private readonly GroupRepository       $groupRepo,
        private readonly GroupMemberRepository $groupMemberRepo,
        private readonly GroupPolicy           $policy,
        private readonly GroupValidator        $validator,
        private readonly ActivityLogService    $activityLog,
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        if ($this->policy->canViewAll($jwt)) {
            return $this->groupRepo->paginateForAdmin($filters, $perPage, $page);
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);

        return $this->groupRepo->paginateForParent($profileId, $filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): ParentGroup
    {
        if (!$this->policy->canCreate($jwt)) {
            throw new UnauthorizedException('Only admins can create groups.');
        }

        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto     = CreateGroupDTO::fromArray($data);
        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;

        return DB::transaction(function () use ($dto, $actorId) {
            $group = ParentGroup::create([
                'group_name'  => $dto->groupName,
                'description' => $dto->description,
                'visibility'  => $dto->visibility,
                'status'      => 'active',
            ]);

            $this->activityLog->log(
                $actorId,
                'created',
                'parent_groups',
                $group->id,
                null,
                ['group_name' => $group->group_name, 'visibility' => $group->visibility]
            );

            return $group;
        });
    }

    public function show(int $id, array $jwt): ParentGroup
    {
        $group = $this->groupRepo->findById($id);
        if (!$group) {
            throw new NotFoundException('Group not found.');
        }

        if (!$this->policy->canView($jwt, $id, $group->visibility)) {
            throw new UnauthorizedException('You do not have permission to view this group.');
        }

        return $group;
    }

    public function update(int $id, array $data, array $jwt): ParentGroup
    {
        if (!$this->policy->canEdit($jwt)) {
            throw new UnauthorizedException('Only admins can update groups.');
        }

        $group = $this->groupRepo->findById($id);
        if (!$group) {
            throw new NotFoundException('Group not found.');
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $actorId   = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $oldValues = [
            'group_name'  => $group->group_name,
            'visibility'  => $group->visibility,
            'status'      => $group->status,
        ];

        return DB::transaction(function () use ($group, $data, $oldValues, $actorId) {
            $updates = [];
            foreach (['group_name', 'description', 'visibility', 'status'] as $field) {
                if (isset($data[$field])) {
                    $updates[$field] = $data[$field];
                }
            }

            if (!empty($updates)) {
                $group->update($updates);
            }

            $this->activityLog->log(
                $actorId,
                'updated',
                'parent_groups',
                $group->id,
                $oldValues,
                $updates
            );

            return $group->fresh();
        });
    }

    public function listMembers(int $groupId, array $jwt): array
    {
        $group = $this->groupRepo->findById($groupId);
        if (!$group) {
            throw new NotFoundException('Group not found.');
        }

        if (!$this->policy->canViewMembers($jwt, $groupId)) {
            throw new UnauthorizedException('You do not have permission to view members of this group.');
        }

        return $this->groupMemberRepo->getActiveMembersForGroup($groupId);
    }

    public function join(int $groupId, array $jwt): GroupMember
    {
        if (!$this->policy->canJoin($jwt)) {
            throw new UnauthorizedException('Only parents can join groups.');
        }

        $group = $this->groupRepo->findById($groupId);
        if (!$group) {
            throw new NotFoundException('Group not found.');
        }

        if ($group->status === 'archived') {
            throw new BusinessRuleException('GROUP_ARCHIVED', 'Cannot join an archived group.');
        }

        if ($group->visibility !== 'public') {
            throw new UnauthorizedException('Only public groups can be joined directly.');
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        $actorId   = $profileId ?: null;
        $existing  = $this->groupMemberRepo->findByGroupAndMember($groupId, $profileId);

        if ($existing !== null) {
            if ($existing->status === 'banned') {
                throw new UnauthorizedException('You are banned from this group.');
            }
            if ($existing->status === 'active') {
                throw new DuplicateException('You are already a member of this group.');
            }

            // status === 'left' — rejoin by resetting the existing record
            return DB::transaction(function () use ($existing, $groupId, $profileId, $actorId) {
                $existing->update(['status' => 'active', 'joined_at' => now()]);

                $this->activityLog->log(
                    $actorId,
                    'group_joined',
                    'parent_groups',
                    $groupId,
                    ['status' => 'left'],
                    ['status' => 'active', 'member_id' => $profileId]
                );

                return $existing->fresh();
            });
        }

        return DB::transaction(function () use ($groupId, $profileId, $actorId) {
            $member = GroupMember::create([
                'group_id'  => $groupId,
                'member_id' => $profileId,
                'joined_at' => now(),
                'status'    => 'active',
            ]);

            $this->activityLog->log(
                $actorId,
                'group_joined',
                'parent_groups',
                $groupId,
                null,
                ['member_id' => $profileId]
            );

            return $member;
        });
    }

    public function leave(int $groupId, array $jwt): void
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);
        $existing  = $this->groupMemberRepo->findByGroupAndMember($groupId, $profileId);

        if (!$existing || $existing->status !== 'active') {
            throw new NotFoundException('You are not an active member of this group.');
        }

        $actorId = $profileId ?: null;

        DB::transaction(function () use ($existing, $groupId, $profileId, $actorId) {
            $existing->update(['status' => 'left']);

            $this->activityLog->log(
                $actorId,
                'group_left',
                'parent_groups',
                $groupId,
                ['status' => 'active'],
                ['status' => 'left', 'member_id' => $profileId]
            );
        });
    }

    public function removeMember(int $groupId, int $memberId, array $data, array $jwt): void
    {
        if (!$this->policy->canManageMember($jwt)) {
            throw new UnauthorizedException('Only admins can remove or ban group members.');
        }

        $errors = $this->validator->validateMemberAction($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $existing = $this->groupMemberRepo->findByGroupAndMember($groupId, $memberId);
        if (!$existing) {
            throw new NotFoundException('Member not found in this group.');
        }

        $action    = $data['action'];
        $actorId   = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $oldStatus = $existing->status;
        $newStatus = $action === 'ban' ? 'banned' : 'left';
        $logAction = $action === 'ban' ? 'group_member_banned' : 'group_member_removed';

        DB::transaction(function () use ($existing, $groupId, $memberId, $newStatus, $logAction, $oldStatus, $actorId) {
            $existing->update(['status' => $newStatus]);

            $this->activityLog->log(
                $actorId,
                $logAction,
                'parent_groups',
                $groupId,
                ['status' => $oldStatus],
                ['status' => $newStatus, 'member_id' => $memberId]
            );
        });
    }
}
