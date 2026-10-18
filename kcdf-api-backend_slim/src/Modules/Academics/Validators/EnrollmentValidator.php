<?php

declare(strict_types=1);

namespace App\Modules\Academics\Validators;

class EnrollmentValidator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['family_id'])) {
            $errors['family_id'] = ['The family_id field is required.'];
        } elseif (!is_numeric($data['family_id'])) {
            $errors['family_id'] = ['The family_id must be a valid integer.'];
        }

        if (empty($data['member_id'])) {
            $errors['member_id'] = ['The member_id field is required.'];
        } elseif (!is_numeric($data['member_id'])) {
            $errors['member_id'] = ['The member_id must be a valid integer.'];
        }

        if (empty($data['batch_id'])) {
            $errors['batch_id'] = ['The batch_id field is required.'];
        } elseif (!is_numeric($data['batch_id'])) {
            $errors['batch_id'] = ['The batch_id must be a valid integer.'];
        }

        return $errors;
    }

    public function validateCancel(array $data): array
    {
        $errors = [];

        if (!isset($data['status']) || $data['status'] !== 'cancelled') {
            $errors['status'] = ['The status must be "cancelled".'];
        }

        return $errors;
    }
}
