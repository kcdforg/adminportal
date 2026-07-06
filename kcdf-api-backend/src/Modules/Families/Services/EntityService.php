<?php

declare(strict_types=1);

namespace App\Modules\Families\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Families\Models\Entity;
use App\Modules\Families\Models\EntityMemberRelation;
use App\Modules\Families\Policies\MemberPolicy;
use App\Modules\Families\Repositories\EntityRepository;
use App\Modules\Families\Repositories\MemberRepository;
use App\Modules\Families\Validators\EntityValidator;

class EntityService
{
    public function __construct(
        private readonly EntityRepository  $entityRepo,
        private readonly MemberRepository  $memberRepo,
        private readonly MemberPolicy      $memberPolicy,
        private readonly EntityValidator   $validator,
        private readonly ActivityLogService $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->entityRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Entity
    {
        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $entity = $this->entityRepo->create([
            'entity_type' => $data['entity_type'],
            'name'        => $data['name'],
            'city'        => $data['city'] ?? null,
            'state'       => $data['state'] ?? null,
            'country'     => $data['country'] ?? 'India',
            'meta'        => !empty($data['meta']) ? json_encode($data['meta']) : null,
            'status'      => 'active',
        ]);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'entities', $entity->id, null, $entity->toArray());

        return $entity;
    }

    public function show(int $id, array $jwt): Entity
    {
        $entity = $this->entityRepo->findById($id);
        if (!$entity) {
            throw new NotFoundException('Entity not found.');
        }
        return $entity;
    }

    public function update(int $id, array $data, array $jwt): Entity
    {
        $entity = $this->entityRepo->findById($id);
        if (!$entity) {
            throw new NotFoundException('Entity not found.');
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $allowed    = ['entity_type', 'name', 'city', 'state', 'country', 'meta', 'status'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        if (array_key_exists('meta', $updateData) && is_array($updateData['meta'])) {
            $updateData['meta'] = json_encode($updateData['meta']);
        }

        $oldValues = $entity->toArray();
        $updated   = $this->entityRepo->update($entity, $updateData);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'entities', $id, $oldValues, $updated->toArray());

        return $updated;
    }

    public function listRelations(int $memberId, array $jwt): array
    {
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new NotFoundException('Member profile not found.');
        }

        if (!$this->memberPolicy->canView($jwt, $memberId)) {
            throw new UnauthorizedException();
        }

        return $this->entityRepo->getRelationsForMember($memberId)->toArray();
    }

    public function addRelation(int $memberId, array $data, array $jwt): EntityMemberRelation
    {
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new NotFoundException('Member profile not found.');
        }

        if (!$this->memberPolicy->canManageEntityRelations($jwt, $memberId)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateRelation($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $entityId = (int) $data['entity_id'];
        if (!$this->entityRepo->entityExists($entityId)) {
            throw new NotFoundException('Entity not found.');
        }

        $isCurrent = isset($data['is_current']) ? (bool) $data['is_current'] : true;

        $relation = $this->entityRepo->createRelation([
            'member_id'        => $memberId,
            'entity_id'        => $entityId,
            'relation_type'    => $data['relation_type'],
            'start_date'       => $data['start_date'] ?? null,
            'end_date'         => $data['end_date'] ?? null,
            'is_current'       => $isCurrent,
            'relation_context' => !empty($data['relation_context']) ? json_encode($data['relation_context']) : null,
        ]);

        $relation->load('entity');

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'add_entity_relation', 'member_profiles', $memberId, null, $relation->toArray());

        return $relation;
    }

    public function removeRelation(int $memberId, int $relationId, array $jwt): void
    {
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new NotFoundException('Member profile not found.');
        }

        if (!$this->memberPolicy->canManageEntityRelations($jwt, $memberId)) {
            throw new UnauthorizedException();
        }

        $relation = $this->entityRepo->findRelation($relationId);
        if (!$relation || (int) $relation->member_id !== $memberId) {
            throw new NotFoundException('Entity relation not found.');
        }

        $oldValues = $relation->toArray();
        $this->entityRepo->deleteRelation($relation);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'remove_entity_relation', 'member_profiles', $memberId, $oldValues, null);
    }
}
