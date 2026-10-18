<?php

declare(strict_types=1);

namespace App\Modules\Academics\DTOs;

class AttendanceRecordDTO
{
    public function __construct(
        public readonly int     $memberId,
        public readonly string  $attendanceStatus,
        public readonly ?string $remarks = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            memberId:         (int) $data['member_id'],
            attendanceStatus: $data['attendance_status'],
            remarks:          $data['remarks'] ?? null,
        );
    }
}
