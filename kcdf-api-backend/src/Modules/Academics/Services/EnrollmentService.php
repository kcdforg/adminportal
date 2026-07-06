<?php

declare(strict_types=1);

namespace App\Modules\Academics\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\DTOs\CreateEnrollmentDTO;
use App\Modules\Academics\Models\BatchMember;
use App\Modules\Academics\Models\Enrollment;
use App\Modules\Academics\Policies\EnrollmentPolicy;
use App\Modules\Academics\Repositories\BatchMemberRepository;
use App\Modules\Academics\Repositories\BatchRepository;
use App\Modules\Academics\Repositories\EnrollmentRepository;
use App\Modules\Academics\Validators\EnrollmentValidator;
use App\Modules\Families\Repositories\FamilyMemberRepository;
use App\Modules\Families\Repositories\FamilyRepository;
use Illuminate\Database\Capsule\Manager as DB;

class EnrollmentService
{
    public function __construct(
        private readonly EnrollmentRepository  $enrollmentRepo,
        private readonly BatchRepository       $batchRepo,
        private readonly BatchMemberRepository $batchMemberRepo,
        private readonly FamilyRepository      $familyRepo,
        private readonly FamilyMemberRepository $familyMemberRepo,
        private readonly EnrollmentPolicy      $policy,
        private readonly EnrollmentValidator   $validator,
        private readonly ActivityLogService    $activityLog
    ) {}

    public function list(array $filters, array $jwt): array
    {
        $perPage = min((int) ($filters['per_page'] ?? 20), 100);
        $page    = max((int) ($filters['page'] ?? 1), 1);

        if ($this->isAdmin($jwt)) {
            return $this->enrollmentRepo->paginateFiltered($filters, $perPage, $page);
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        $familyIds = $jwt['family_ids'] ?? [];

        if (empty($familyIds)) {
            return $this->emptyPage($perPage);
        }

        if (!empty($filters['family_id']) && in_array((int) $filters['family_id'], $familyIds, true)) {
            return $this->enrollmentRepo->paginateForFamily((int) $filters['family_id'], $filters, $perPage, $page);
        }

        $filters['family_id'] = $familyIds[0];
        return $this->enrollmentRepo->paginateForFamily((int) $filters['family_id'], $filters, $perPage, $page);
    }

    public function create(array $data, array $jwt): Enrollment
    {
        $errors = $this->validator->validateCreate($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $dto = CreateEnrollmentDTO::fromArray($data);

        if (!$this->policy->canCreate($jwt, $dto->familyId)) {
            throw new UnauthorizedException();
        }

        $family = $this->familyRepo->findById($dto->familyId);
        if (!$family) {
            throw new NotFoundException('Family not found.');
        }

        $membership = $this->familyMemberRepo->findByFamilyAndProfile($dto->familyId, $dto->memberId);
        if (!$membership) {
            throw new ValidationException([
                'member_id' => ['The member does not belong to the specified family.'],
            ]);
        }

        $batch = $this->batchRepo->findWithRelations($dto->batchId);
        if (!$batch) {
            throw new NotFoundException('Batch not found.');
        }

        if (!in_array($batch->status, ['upcoming', 'active'], true)) {
            throw new BusinessRuleException(
                'BATCH_NOT_ENROLLABLE',
                'Enrollment is only allowed for upcoming or active batches.'
            );
        }

        if ($this->enrollmentRepo->findByMemberAndBatch($dto->memberId, $dto->batchId)) {
            throw new BusinessRuleException('ENROLLMENT_EXISTS', 'This member is already enrolled in this batch.');
        }

        if ($batch->capacity !== null) {
            $activeCount = $this->batchRepo->countActiveMembers($dto->batchId);
            if ($activeCount >= $batch->capacity) {
                throw new BusinessRuleException('BATCH_FULL', 'This batch has reached its maximum capacity.');
            }
        }

        $feeAmount = $batch->program?->fee_amount ?? 0;
        $actorId   = (int) ($jwt['profile_id'] ?? 0) ?: null;

        return DB::transaction(function () use ($dto, $feeAmount, $actorId) {
            $enrollment = Enrollment::create([
                'family_id'            => $dto->familyId,
                'member_id'            => $dto->memberId,
                'batch_id'             => $dto->batchId,
                'enrolled_by_member_id' => $actorId,
                'enrolled_at'          => now(),
                'status'               => 'active',
                'payment_status'       => 'unpaid',
                'fee_amount'           => $feeAmount,
            ]);

            BatchMember::create([
                'batch_id'  => $dto->batchId,
                'member_id' => $dto->memberId,
                'joined_at' => now(),
                'status'    => 'active',
            ]);

            $enrollment = $this->enrollmentRepo->findWithRelations($enrollment->id);

            $this->activityLog->log($actorId, 'create', 'enrollments', $enrollment->id, null, $enrollment->toArray());

            return $enrollment;
        });
    }

    public function show(int $id, array $jwt): Enrollment
    {
        $enrollment = $this->enrollmentRepo->findWithRelations($id);
        if (!$enrollment) {
            throw new NotFoundException('Enrollment not found.');
        }

        if (!$this->policy->canView($jwt, $enrollment)) {
            throw new UnauthorizedException();
        }

        return $enrollment;
    }

    public function cancel(int $id, array $jwt): Enrollment
    {
        $enrollment = $this->enrollmentRepo->findWithRelations($id);
        if (!$enrollment) {
            throw new NotFoundException('Enrollment not found.');
        }

        if (!$this->policy->canCancel($jwt, $enrollment)) {
            throw new UnauthorizedException();
        }

        if ($enrollment->status === 'cancelled') {
            throw new BusinessRuleException('ALREADY_CANCELLED', 'Enrollment is already cancelled.');
        }

        $oldValues = $enrollment->toArray();

        return DB::transaction(function () use ($enrollment, $oldValues) {
            $this->enrollmentRepo->update($enrollment, ['status' => 'cancelled']);

            $batchMember = $this->batchMemberRepo->findByBatchAndMember(
                $enrollment->batch_id,
                $enrollment->member_id
            );
            if ($batchMember) {
                $this->batchMemberRepo->update($batchMember, ['status' => 'dropped']);
            }

            $updated = $this->enrollmentRepo->findWithRelations($enrollment->id);

            $actorId = null;
            $this->activityLog->log($actorId, 'cancel', 'enrollments', $enrollment->id, $oldValues, $updated->toArray());

            return $updated;
        });
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }

    private function emptyPage(int $perPage): array
    {
        return [
            'data' => [],
            'meta' => ['total' => 0, 'per_page' => $perPage, 'current_page' => 1, 'last_page' => 1],
        ];
    }
}
