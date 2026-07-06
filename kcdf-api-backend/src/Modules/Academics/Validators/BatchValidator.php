<?php

declare(strict_types=1);

namespace App\Modules\Academics\Validators;

class BatchValidator
{
    private const VALID_STATUSES = ['upcoming', 'active', 'completed', 'cancelled'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['program_id'])) {
            $errors['program_id'] = ['The program_id field is required.'];
        } elseif (!is_numeric($data['program_id'])) {
            $errors['program_id'] = ['The program_id must be a valid integer.'];
        }

        if (empty($data['batch_name'])) {
            $errors['batch_name'] = ['The batch_name field is required.'];
        } elseif (strlen($data['batch_name']) > 255) {
            $errors['batch_name'] = ['The batch_name may not be greater than 255 characters.'];
        }

        if (isset($data['capacity'])) {
            if (!is_numeric($data['capacity']) || (int) $data['capacity'] < 1) {
                $errors['capacity'] = ['The capacity must be an integer of at least 1.'];
            }
        }

        if (!empty($data['start_date']) && !$this->isValidDate($data['start_date'])) {
            $errors['start_date'] = ['The start_date must be a valid date (YYYY-MM-DD).'];
        }

        if (!empty($data['end_date'])) {
            if (!$this->isValidDate($data['end_date'])) {
                $errors['end_date'] = ['The end_date must be a valid date (YYYY-MM-DD).'];
            } elseif (!empty($data['start_date']) && $this->isValidDate($data['start_date']) && $data['end_date'] <= $data['start_date']) {
                $errors['end_date'] = ['The end_date must be after the start_date.'];
            }
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('batch_name', $data)) {
            if (empty($data['batch_name'])) {
                $errors['batch_name'] = ['The batch_name field cannot be empty.'];
            } elseif (strlen($data['batch_name']) > 255) {
                $errors['batch_name'] = ['The batch_name may not be greater than 255 characters.'];
            }
        }

        if (array_key_exists('capacity', $data) && $data['capacity'] !== null) {
            if (!is_numeric($data['capacity']) || (int) $data['capacity'] < 1) {
                $errors['capacity'] = ['The capacity must be an integer of at least 1.'];
            }
        }

        if (array_key_exists('status', $data) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: upcoming, active, completed, cancelled.'];
        }

        if (!empty($data['start_date']) && !$this->isValidDate($data['start_date'])) {
            $errors['start_date'] = ['The start_date must be a valid date (YYYY-MM-DD).'];
        }

        if (!empty($data['end_date'])) {
            if (!$this->isValidDate($data['end_date'])) {
                $errors['end_date'] = ['The end_date must be a valid date (YYYY-MM-DD).'];
            } elseif (!empty($data['start_date']) && $this->isValidDate($data['start_date']) && $data['end_date'] <= $data['start_date']) {
                $errors['end_date'] = ['The end_date must be after the start_date.'];
            }
        }

        return $errors;
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
