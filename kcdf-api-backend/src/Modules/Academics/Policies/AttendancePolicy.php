<?php

declare(strict_types=1);

namespace App\Modules\Academics\Policies;

use App\Modules\Academics\Models\Attendance;
use App\Modules\Academics\Models\BatchSession;
use App\Modules\Families\Repositories\TrainerRepository;

class AttendancePolicy
{
    public function __construct(private readonly TrainerRepository $trainerRepo) {}

    public function canViewSessionAttendance(array $jwt, BatchSession $session): bool
    {
        if ($this->isElevatedAdmin($jwt)) {
            return true;
        }

        return $this->isTrainer($jwt) && $this->isSessionTrainer($jwt, $session);
    }

    public function canMarkAttendance(array $jwt, BatchSession $session): bool
    {
        if ($this->isElevatedAdmin($jwt)) {
            return true;
        }

        return $this->isTrainer($jwt) && $this->isSessionTrainer($jwt, $session);
    }

    public function canPatchAttendance(array $jwt, Attendance $record): bool
    {
        if ($this->isElevatedAdmin($jwt)) {
            return true;
        }

        if (!$this->isTrainer($jwt)) {
            return false;
        }

        $session = $record->session;
        if (!$session) {
            return false;
        }

        return $this->isSessionTrainer($jwt, $session);
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
