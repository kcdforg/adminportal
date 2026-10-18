<?php

declare(strict_types=1);

namespace App\Modules\Academics\Policies;

use App\Modules\Academics\Repositories\EnrollmentRepository;
use App\Modules\Families\Repositories\TrainerRepository;

class BatchPolicy
{
    public function __construct(
        private readonly TrainerRepository   $trainerRepo,
        private readonly EnrollmentRepository $enrollmentRepo
    ) {}

    public function canViewAll(array $jwt): bool
    {
        return $this->isAdmin($jwt);
    }

    public function canCreate(array $jwt): bool
    {
        return $this->isElevatedAdmin($jwt);
    }

    public function canEdit(array $jwt): bool
    {
        return $this->isElevatedAdmin($jwt);
    }

    public function canView(array $jwt, int $batchId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return false;
        }

        if ($this->isTrainer($jwt)) {
            $trainer = $this->trainerRepo->findByProfileId($profileId);
            if ($trainer && $trainer->id === $this->getBatchTrainerId($batchId)) {
                return true;
            }
        }

        return $this->isMemberEnrolledInBatch($profileId, $batchId);
    }

    public function canViewMembers(array $jwt, int $batchId): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId || !$this->isTrainer($jwt)) {
            return false;
        }

        $trainer = $this->trainerRepo->findByProfileId($profileId);
        return $trainer && $trainer->id === $this->getBatchTrainerId($batchId);
    }

    public function canViewSessions(array $jwt, int $batchId): bool
    {
        return $this->canView($jwt, $batchId);
    }

    private function getBatchTrainerId(int $batchId): ?int
    {
        $batch = \App\Modules\Academics\Models\StudentBatch::find($batchId);
        return $batch?->trainer_id;
    }

    private function isMemberEnrolledInBatch(int $profileId, int $batchId): bool
    {
        return \App\Modules\Academics\Models\Enrollment::where('member_id', $profileId)
            ->where('batch_id', $batchId)
            ->whereIn('status', ['active', 'pending'])
            ->exists();
    }

    private function isAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager', 'admin_accounts', 'admin_readonly']
        ));
    }

    private function isElevatedAdmin(array $jwt): bool
    {
        return !empty(array_intersect(
            $jwt['roles'] ?? [],
            ['admin_super', 'admin_program_manager']
        ));
    }

    private function isTrainer(array $jwt): bool
    {
        return in_array('trainer', $jwt['roles'] ?? [], true);
    }
}
