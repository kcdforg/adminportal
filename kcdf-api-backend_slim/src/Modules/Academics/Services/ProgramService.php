<?php

declare(strict_types=1);

namespace App\Modules\Academics\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\DTOs\CreateProgramDTO;
use App\Modules\Academics\Models\Program;
use App\Modules\Academics\Repositories\ProgramRepository;
use App\Modules\Academics\Validators\ProgramValidator;

class ProgramService
{
    public function __construct(
        private readonly ProgramRepository $programRepo,
        private readonly ProgramValidator  $validator,
        private readonly ActivityLogService $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        return $this->programRepo->paginateFiltered($filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Program
    {
        if (!$this->isElevatedAdmin($jwt)) {
            throw new UnauthorizedException();
        }

        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto     = CreateProgramDTO::fromArray($data);
        $program = Program::create($dto->toArray());

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'create', 'programs', $program->id, null, $program->toArray());

        return $program;
    }

    public function show(int $id, array $jwt): Program
    {
        $program = $this->programRepo->findById($id);
        if (!$program) {
            throw new NotFoundException('Program not found.');
        }

        return $program;
    }

    public function update(int $id, array $data, array $jwt): Program
    {
        if (!$this->isElevatedAdmin($jwt)) {
            throw new UnauthorizedException();
        }

        $program = $this->programRepo->findById($id);
        if (!$program) {
            throw new NotFoundException('Program not found.');
        }

        $errors = $this->validator->validateUpdate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues  = $program->toArray();
        $updateData = array_intersect_key($data, array_flip([
            'program_name', 'program_type', 'description', 'age_group', 'fee_amount', 'status',
        ]));

        $this->programRepo->update($program, $updateData);
        $updated = $this->programRepo->findById($id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'update', 'programs', $id, $oldValues, $updated->toArray());

        return $updated;
    }

    public function updateStatus(int $id, array $data, array $jwt): Program
    {
        if (!$this->isElevatedAdmin($jwt)) {
            throw new UnauthorizedException();
        }

        $program = $this->programRepo->findById($id);
        if (!$program) {
            throw new NotFoundException('Program not found.');
        }

        $errors = $this->validator->validateStatus($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues = $program->toArray();
        $this->programRepo->update($program, ['status' => $data['status']]);
        $updated = $this->programRepo->findById($id);

        $actorId = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $this->activityLog->log($actorId, 'status_change', 'programs', $id, $oldValues, $updated->toArray());

        return $updated;
    }

    private function isElevatedAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager']
        ));
    }
}
