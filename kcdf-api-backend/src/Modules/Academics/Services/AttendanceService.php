<?php

declare(strict_types=1);

namespace App\Modules\Academics\Services;

use App\Core\ActivityLogService;
use App\Core\Exceptions\BusinessRuleException;
use App\Core\Exceptions\NotFoundException;
use App\Core\Exceptions\UnauthorizedException;
use App\Core\Exceptions\ValidationException;
use App\Modules\Academics\DTOs\AttendanceRecordDTO;
use App\Modules\Academics\Models\Attendance;
use App\Modules\Academics\Policies\AttendancePolicy;
use App\Modules\Academics\Repositories\AttendanceRepository;
use App\Modules\Academics\Repositories\BatchMemberRepository;
use App\Modules\Academics\Repositories\SessionRepository;
use App\Modules\Academics\Validators\AttendanceValidator;

class AttendanceService
{
    public function __construct(
        private readonly AttendanceRepository  $attendanceRepo,
        private readonly SessionRepository     $sessionRepo,
        private readonly BatchMemberRepository $batchMemberRepo,
        private readonly AttendancePolicy      $policy,
        private readonly AttendanceValidator   $validator,
        private readonly ActivityLogService    $activityLog
    ) {}

    public function listForSession(int $sessionId, array $jwt): array
    {
        $session = $this->sessionRepo->findWithRelations($sessionId);
        if (!$session) {
            throw new NotFoundException('Session not found.');
        }

        if (!$this->policy->canViewSessionAttendance($jwt, $session)) {
            throw new UnauthorizedException();
        }

        return $this->attendanceRepo->getForSession($sessionId);
    }

    public function bulkSubmit(int $sessionId, array $data, array $jwt): int
    {
        $session = $this->sessionRepo->findWithRelations($sessionId);
        if (!$session) {
            throw new NotFoundException('Session not found.');
        }

        if (!$this->policy->canMarkAttendance($jwt, $session)) {
            throw new UnauthorizedException();
        }

        if ($session->attendance_locked) {
            throw new BusinessRuleException('ATTENDANCE_LOCKED', 'Attendance for this session is locked.');
        }

        $errors = $this->validator->validateBulk($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $batchMemberIds = $this->batchMemberRepo->getMemberIdsForBatch($session->batch_id);
        $actorId        = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $markedAt       = date('Y-m-d H:i:s');
        $count          = 0;

        foreach ($data['records'] as $rawRecord) {
            $dto = AttendanceRecordDTO::fromArray($rawRecord);

            if (!in_array($dto->memberId, $batchMemberIds, true)) {
                throw new ValidationException([
                    "records.member_id" => ["Member #{$dto->memberId} is not enrolled in this batch."],
                ]);
            }

            $this->attendanceRepo->upsert($sessionId, $dto->memberId, [
                'attendance_status'   => $dto->attendanceStatus,
                'remarks'             => $dto->remarks,
                'marked_by_member_id' => $actorId,
                'marked_at'           => $markedAt,
            ]);

            $count++;
        }

        $this->activityLog->log(
            $actorId,
            'bulk_attendance',
            'batch_sessions',
            $sessionId,
            null,
            ['records_saved' => $count]
        );

        return $count;
    }

    public function patch(int $id, array $data, array $jwt): Attendance
    {
        $record = $this->attendanceRepo->findById($id);
        if (!$record) {
            throw new NotFoundException('Attendance record not found.');
        }

        $record->load('session.batch');

        if (!$this->policy->canPatchAttendance($jwt, $record)) {
            throw new UnauthorizedException();
        }

        if ($record->session && $record->session->attendance_locked) {
            throw new BusinessRuleException('ATTENDANCE_LOCKED', 'Attendance for this session is locked.');
        }

        $errors = $this->validator->validatePatch($data);
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        $oldValues  = $record->toArray();
        $actorId    = (int) ($jwt['profile_id'] ?? 0) ?: null;
        $updateData = array_intersect_key($data, array_flip(['attendance_status', 'remarks']));
        $updateData['marked_by_member_id'] = $actorId;
        $updateData['marked_at']           = date('Y-m-d H:i:s');

        $this->attendanceRepo->update($record, $updateData);
        $updated = $this->attendanceRepo->findById($id);

        $this->activityLog->log($actorId, 'update', 'attendance', $id, $oldValues, $updated->toArray());

        return $updated;
    }
}
