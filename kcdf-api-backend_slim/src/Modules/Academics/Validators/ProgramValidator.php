<?php

declare(strict_types=1);

namespace App\Modules\Academics\Validators;

class ProgramValidator
{
    private const VALID_TYPES   = ['class', 'workshop', 'camp', 'event'];
    private const VALID_STATUSES = ['active', 'inactive', 'archived'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['program_name'])) {
            $errors['program_name'] = ['The program_name field is required.'];
        } elseif (strlen($data['program_name']) > 255) {
            $errors['program_name'] = ['The program_name may not be greater than 255 characters.'];
        }

        if (empty($data['program_type'])) {
            $errors['program_type'] = ['The program_type field is required.'];
        } elseif (!in_array($data['program_type'], self::VALID_TYPES, true)) {
            $errors['program_type'] = ['The program_type must be one of: class, workshop, camp, event.'];
        }

        if (!isset($data['fee_amount'])) {
            $errors['fee_amount'] = ['The fee_amount field is required.'];
        } elseif (!is_numeric($data['fee_amount']) || (float) $data['fee_amount'] < 0) {
            $errors['fee_amount'] = ['The fee_amount must be a non-negative number.'];
        } elseif (strlen((string) ((float) $data['fee_amount'] - floor((float) $data['fee_amount']))) > 4) {
            $errors['fee_amount'] = ['The fee_amount may not have more than 2 decimal places.'];
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('program_name', $data)) {
            if (empty($data['program_name'])) {
                $errors['program_name'] = ['The program_name field cannot be empty.'];
            } elseif (strlen($data['program_name']) > 255) {
                $errors['program_name'] = ['The program_name may not be greater than 255 characters.'];
            }
        }

        if (array_key_exists('program_type', $data) && !in_array($data['program_type'], self::VALID_TYPES, true)) {
            $errors['program_type'] = ['The program_type must be one of: class, workshop, camp, event.'];
        }

        if (array_key_exists('fee_amount', $data)) {
            if (!is_numeric($data['fee_amount']) || (float) $data['fee_amount'] < 0) {
                $errors['fee_amount'] = ['The fee_amount must be a non-negative number.'];
            }
        }

        if (array_key_exists('status', $data) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive, archived.'];
        }

        return $errors;
    }

    public function validateStatus(array $data): array
    {
        $errors = [];

        if (empty($data['status'])) {
            $errors['status'] = ['The status field is required.'];
        } elseif (!in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive, archived.'];
        }

        return $errors;
    }
}
