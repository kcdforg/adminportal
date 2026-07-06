<?php

declare(strict_types=1);

namespace App\Modules\Academics\Validators;

class SessionValidator
{
    private const VALID_TYPES    = ['regular', 'special', 'exam', 'workshop'];
    private const VALID_STATUSES = ['scheduled', 'completed', 'cancelled', 'postponed'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['session_date'])) {
            $errors['session_date'] = ['The session_date field is required.'];
        } elseif (!$this->isValidDate($data['session_date'])) {
            $errors['session_date'] = ['The session_date must be a valid date (YYYY-MM-DD).'];
        }

        if (empty($data['session_type'])) {
            $errors['session_type'] = ['The session_type field is required.'];
        } elseif (!in_array($data['session_type'], self::VALID_TYPES, true)) {
            $errors['session_type'] = ['The session_type must be one of: regular, special, exam, workshop.'];
        }

        if (!empty($data['start_time']) && !$this->isValidTime($data['start_time'])) {
            $errors['start_time'] = ['The start_time must be a valid time (HH:MM).'];
        }

        if (!empty($data['end_time'])) {
            if (!$this->isValidTime($data['end_time'])) {
                $errors['end_time'] = ['The end_time must be a valid time (HH:MM).'];
            } elseif (!empty($data['start_time']) && $this->isValidTime($data['start_time']) && $data['end_time'] <= $data['start_time']) {
                $errors['end_time'] = ['The end_time must be after the start_time.'];
            }
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('session_date', $data) && !empty($data['session_date'])) {
            if (!$this->isValidDate($data['session_date'])) {
                $errors['session_date'] = ['The session_date must be a valid date (YYYY-MM-DD).'];
            }
        }

        if (array_key_exists('session_type', $data) && !in_array($data['session_type'], self::VALID_TYPES, true)) {
            $errors['session_type'] = ['The session_type must be one of: regular, special, exam, workshop.'];
        }

        if (array_key_exists('status', $data) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: scheduled, completed, cancelled, postponed.'];
        }

        if (!empty($data['start_time']) && !$this->isValidTime($data['start_time'])) {
            $errors['start_time'] = ['The start_time must be a valid time (HH:MM).'];
        }

        if (!empty($data['end_time'])) {
            if (!$this->isValidTime($data['end_time'])) {
                $errors['end_time'] = ['The end_time must be a valid time (HH:MM).'];
            } elseif (!empty($data['start_time']) && $this->isValidTime($data['start_time']) && $data['end_time'] <= $data['start_time']) {
                $errors['end_time'] = ['The end_time must be after the start_time.'];
            }
        }

        return $errors;
    }

    public function validateTrainerUpdate(array $data): array
    {
        return [];
    }

    private function isValidDate(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $time);
    }
}
