<?php

declare(strict_types=1);

namespace App\Modules\Academics\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\DTOs\CreateSessionDTO;
use App\Modules\Academics\Models\BatchSession;
use App\Modules\Academics\Policies\BatchPolicy;
use App\Modules\Academics\Policies\SessionPolicy;
use App\Modules\Academics\Repositories\BatchRepository;
use App\Modules\Academics\Repositories\SessionRepository;
use App\Modules\Academics\Validators\SessionValidator;

class SessionService
{
    public function __construct(
        private readonly SessionRepository  $sessionRepo,
        private readonly BatchRepository    $batchRepo,
        private readonly BatchPolicy        $batchPolicy,
        private readonly SessionPolicy      $sessionPolicy,
        private readonly SessionValidator   $validator,
        private readonly ActivityLogService $activityLog
    ) {}

    public function listForBatch(int $batchId, array $filters, array $jwt): array
    {
        $batch = $this->batchRepo->findById($batchId);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        if (!$this->batchPolicy->canViewSessions($jwt, $batchId)) {
            throw new UnauthorizedException();
        }

        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->sessionRepo->paginateForBatch($batchId, $filters, $perPage, $page);
    }

    public function create(int $batchId, array $data, array $jwt): BatchSession
    {
        if (!$this->sessionPolicy->canLock($jwt)) {
            throw new UnauthorizedException();
        }

        $batch = $this->batchRepo->findById($batchId);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto     = CreateSessionDTO::fromArray($batchId, $data);
        $payload = $dto->toArray();

        if ($payload['trainer_id'] === null) {
            $payload['trainer_id'] = $batch->trainer_id;
        }

        $session = BatchSession::create($payload);
        $session = $this->sessionRepo->findWithRelations($session->id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'batch_sessions', $session->id, null, $session->toArray());

        return $session;
    }

    public function show(int $id, array $jwt): BatchSession
    {
        $session = $this->sessionRepo->findWithRelations($id);
        if (!$session) {
            throw new NotFoundException('Session not found.');
        }

        if (!$this->sessionPolicy->canView($jwt, $session)) {
            throw new UnauthorizedException();
        }

        return $session;
    }

    public function update(int $id, array $data, array $jwt): BatchSession
    {
        $session = $this->sessionRepo->findWithRelations($id);
        if (!$session) {
            throw new NotFoundException('Session not found.');
        }

        if (!$this->sessionPolicy->canEdit($jwt, $session)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues = $session->toArray();

        if ($this->sessionPolicy->canEditFull($jwt)) {
            $updateData = array_intersect_key($data, array_flip([
                'session_number', 'session_title', 'session_date', 'start_time', 'end_time',
                'session_type', 'status', 'trainer_id', 'topics_covered', 'homework', 'notes',
            ]));
        } else {
            $updateData = array_intersect_key($data, array_flip([
                'topics_covered', 'homework', 'notes',
            ]));
        }

        $this->sessionRepo->update($session, $updateData);
        $updated = $this->sessionRepo->findWithRelations($id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'batch_sessions', $id, $oldValues, $updated->toArray());

        return $updated;
    }

    public function lock(int $id, array $jwt): BatchSession
    {
        if (!$this->sessionPolicy->canLock($jwt)) {
            throw new UnauthorizedException();
        }

        $session = $this->sessionRepo->findById($id);
        if (!$session) {
            throw new NotFoundException('Session not found.');
        }

        if ($session->attendance_locked) {
            throw new BusinessRuleException('ALREADY_LOCKED', 'Session attendance is already locked.');
        }

        $oldValues = $session->toArray();
        $this->sessionRepo->update($session, ['attendance_locked' => true]);
        $updated = $this->sessionRepo->findWithRelations($id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'lock', 'batch_sessions', $id, $oldValues, $updated->toArray());

        return $updated;
    }
}
