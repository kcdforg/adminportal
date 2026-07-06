<?php

declare(strict_types=1);

namespace App\Modules\Academics\Validators;

class AttendanceValidator
{
    private const VALID_STATUSES = ['present', 'absent', 'late', 'excused'];

    public function validateBulk(array $data): array
    {
        $errors = [];

        if (empty($data['records']) || !is_array($data['records'])) {
            $errors['records'] = ['The records field is required and must be an array.'];
            return $errors;
        }

        foreach ($data['records'] as $i => $record) {
            if (empty($record['member_id']) || !is_numeric($record['member_id'])) {
                $errors["records.{$i}.member_id"] = ['The member_id field is required and must be a valid integer.'];
            }
            if (empty($record['attendance_status'])) {
                $errors["records.{$i}.attendance_status"] = ['The attendance_status field is required.'];
            } elseif (!in_array($record['attendance_status'], self::VALID_STATUSES, true)) {
                $errors["records.{$i}.attendance_status"] = ['The attendance_status must be one of: present, absent, late, excused.'];
            }
        }

        return $errors;
    }

    public function validatePatch(array $data): array
    {
        $errors = [];

        if (!array_key_exists('attendance_status', $data)) {
            $errors['attendance_status'] = ['The attendance_status field is required.'];
        } elseif (!in_array($data['attendance_status'], self::VALID_STATUSES, true)) {
            $errors['attendance_status'] = ['The attendance_status must be one of: present, absent, late, excused.'];
        }

        return $errors;
    }
}
