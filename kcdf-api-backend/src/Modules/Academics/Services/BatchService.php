<?php

declare(strict_types=1);

namespace App\Modules\Academics\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\DTOs\CreateBatchDTO;
use App\Modules\Academics\Models\StudentBatch;
use App\Modules\Academics\Policies\BatchPolicy;
use App\Modules\Academics\Repositories\BatchMemberRepository;
use App\Modules\Academics\Repositories\BatchRepository;
use App\Modules\Academics\Repositories\ProgramRepository;
use App\Modules\Academics\Validators\BatchValidator;
use App\Modules\Families\Repositories\TrainerRepository;

class BatchService
{
    public function __construct(
        private readonly BatchRepository       $batchRepo,
        private readonly BatchMemberRepository $batchMemberRepo,
        private readonly ProgramRepository     $programRepo,
        private readonly TrainerRepository     $trainerRepo,
        private readonly BatchPolicy           $policy,
        private readonly BatchValidator        $validator,
        private readonly ActivityLogService    $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage   = min((int) ($filters['per_page'] ?? 20), 100);
        $page      = max((int) ($filters['page'] ?? 1), 1);
        $profileId = (int) ($jwt['profile_id'] ?? 0);

        if ($this->isAdmin($jwt)) {
            return $this->batchRepo->paginateFiltered($filters, $perPage, $page);
        }

        if ($this->isTrainer($jwt)) {
            $trainer = $this->trainerRepo->findByProfileId($profileId);
            if (!$trainer) {
                return $this->emptyPage($perPage);
            }
            return $this->batchRepo->paginateForTrainer($trainer->id, $filters, $perPage, $page);
        }

        return $this->batchRepo->paginateForMember($profileId, $filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): StudentBatch
    {
        if (!$this->policy->canCreate($jwt)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $program = $this->programRepo->findById((int) $data['program_id']);
        if (!$program) {
            throw new NotFoundException('Program not found.');
        }

        if (!empty($data['trainer_id'])) {
            $trainer = $this->trainerRepo->findById((int) $data['trainer_id']);
            if (!$trainer) {
                throw new NotFoundException('Trainer not found.');
            }
        }

        $dto   = CreateBatchDTO::fromArray($data);
        $batch = StudentBatch::create($dto->toArray());
        $batch = $this->batchRepo->findWithRelations($batch->id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'student_batches', $batch->id, null, $batch->toArray());

        return $batch;
    }

    public function show(int $id, array $jwt): StudentBatch
    {
        $batch = $this->batchRepo->findWithRelations($id);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        if (!$this->policy->canView($jwt, $id)) {
            throw new UnauthorizedException();
        }

        return $batch;
    }

    public function update(int $id, array $data, array $jwt): StudentBatch
    {
        if (!$this->policy->canEdit($jwt)) {
            throw new UnauthorizedException();
        }

        $batch = $this->batchRepo->findById($id);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if (!empty($data['trainer_id'])) {
            $trainer = $this->trainerRepo->findById((int) $data['trainer_id']);
            if (!$trainer) {
                throw new NotFoundException('Trainer not found.');
            }
        }

        $oldValues  = $batch->toArray();
        $updateData = array_intersect_key($data, array_flip([
            'batch_name', 'capacity', 'trainer_id', 'start_date', 'end_date', 'status',
        ]));

        $this->batchRepo->update($batch, $updateData);
        $updated = $this->batchRepo->findWithRelations($id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'student_batches', $id, $oldValues, $updated->toArray());

        return $updated;
    }

    public function listMembers(int $id, array $jwt): array
    {
        $batch = $this->batchRepo->findById($id);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        if (!$this->policy->canViewMembers($jwt, $id)) {
            throw new UnauthorizedException();
        }

        return $this->batchMemberRepo->getMembersForBatch($id);
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }

    private function isTrainer(array $jwt): bool
    {
        return in_array('trainer', $jwt['roles'] ?? [], true);
    }

    private function emptyPage(int $perPage): array
    {
        return [
            'data' => [],
            'meta' => ['total' => 0, 'per_page' => $perPage, 'current_page' => 1, 'last_page' => 1],
        ];
    }
}
