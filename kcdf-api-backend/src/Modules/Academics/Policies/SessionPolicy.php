<?php

declare(strict_types=1);

namespace App\Modules\Academics\Policies;

use App\Modules\Academics\Models\BatchSession;
use App\Modules\Families\Repositories\TrainerRepository;

class SessionPolicy
{
    public function __construct(private readonly TrainerRepository $trainerRepo) {}

    public function canView(array $jwt, BatchSession $session): bool
    {
        if ($this->isAdmin($jwt)) {
            return true;
        }

        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return false;
        }

        if ($this->isTrainer($jwt) && $this->isSessionTrainer($jwt, $session)) {
            return true;
        }

        return $this->isMemberEnrolledInBatch($profileId, $session->batch_id);
    }

    public function canEdit(array $jwt, BatchSession $session): bool
    {
        if ($this->isElevatedAdmin($jwt)) {
            return true;
        }

        return $this->isTrainer($jwt) && $this->isSessionTrainer($jwt, $session);
    }

    public function canEditFull(array $jwt): bool
    {
        return $this->isElevatedAdmin($jwt);
    }

    public function canLock(array $jwt): bool
    {
        return $this->isElevatedAdmin($jwt);
    }

    public function isSessionTrainer(array $jwt, BatchSession $session): bool
    {
        $profileId = (int) ($jwt['profile_id'] ?? 0);
        if (!$profileId) {
            return false;
        }

        $trainer = $this->trainerRepo->findByProfileId($profileId);
        if (!$trainer) {
            return false;
        }

        $effectiveTrainerId = $session->trainer_id ?? $session->batch?->trainer_id;
        return $trainer->id === $effectiveTrainerId;
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
