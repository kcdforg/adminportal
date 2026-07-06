<?php

declare(strict_types=1);

namespace App\Modules\Families\Validators;

class TrainerValidator
{
    private const VALID_STATUSES = ['active', 'inactive', 'on_leave'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id field is required.'];
        } elseif (!is_numeric($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id must be a valid integer.'];
        }

        if (!empty($data['specialization']) && strlen($data['specialization']) > 255) {
            $errors['specialization'] = ['The specialization may not be greater than 255 characters.'];
        }

        if (array_key_exists('experience_years', $data) && $data['experience_years'] !== null) {
            if (!is_numeric($data['experience_years']) || (int) $data['experience_years'] < 0 || (int) $data['experience_years'] > 60) {
                $errors['experience_years'] = ['The experience_years must be an integer between 0 and 60.'];
            }
        }

        if (!empty($data['joined_at'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['joined_at']);
            if (!$date || $date->format('Y-m-d') !== $data['joined_at']) {
                $errors['joined_at'] = ['The joined_at must be a valid date (YYYY-MM-DD).'];
            }
        }

        if (!empty($data['address'])) {
            $errors = array_merge($errors, $this->validateAddress($data['address']));
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (!empty($data['specialization']) && strlen($data['specialization']) > 255) {
            $errors['specialization'] = ['The specialization may not be greater than 255 characters.'];
        }

        if (array_key_exists('experience_years', $data) && $data['experience_years'] !== null) {
            if (!is_numeric($data['experience_years']) || (int) $data['experience_years'] < 0 || (int) $data['experience_years'] > 60) {
                $errors['experience_years'] = ['The experience_years must be an integer between 0 and 60.'];
            }
        }

        if (!empty($data['joined_at'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['joined_at']);
            if (!$date || $date->format('Y-m-d') !== $data['joined_at']) {
                $errors['joined_at'] = ['The joined_at must be a valid date (YYYY-MM-DD).'];
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive, on_leave.'];
        }

        if (!empty($data['address'])) {
            $errors = array_merge($errors, $this->validateAddress($data['address']));
        }

        return $errors;
    }

    private function validateAddress(array $address): array
    {
        $errors = [];

        if (empty($address['address_line_1'])) {
            $errors['address.address_line_1'] = ['The address.address_line_1 field is required when address is provided.'];
        }

        if (empty($address['city'])) {
            $errors['address.city'] = ['The address.city field is required when address is provided.'];
        }

        if (empty($address['country'])) {
            $errors['address.country'] = ['The address.country field is required when address is provided.'];
        }

        return $errors;
    }
}
