<?php

declare(strict_types=1);

namespace App\Modules\Families\Validators;

class MemberValidator
{
    private const VALID_GENDERS = ['male', 'female', 'other'];
    private const VALID_STATUSES = ['active', 'inactive', 'suspended'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = ['The first_name field is required.'];
        } elseif (strlen($data['first_name']) > 100) {
            $errors['first_name'] = ['The first_name may not be greater than 100 characters.'];
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = ['The last_name field is required.'];
        } elseif (strlen($data['last_name']) > 100) {
            $errors['last_name'] = ['The last_name may not be greater than 100 characters.'];
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['The email must be a valid email address.'];
        }

        if (!empty($data['mobile'])) {
            if (!ctype_digit($data['mobile'])) {
                $errors['mobile'] = ['The mobile must contain digits only.'];
            } elseif (strlen($data['mobile']) < 10) {
                $errors['mobile'] = ['The mobile must be at least 10 digits.'];
            }
        }

        if (!empty($data['gender']) && !in_array($data['gender'], self::VALID_GENDERS, true)) {
            $errors['gender'] = ['The gender must be one of: male, female, other.'];
        }

        if (!empty($data['date_of_birth'])) {
            $dob = \DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
            if (!$dob || $dob->format('Y-m-d') !== $data['date_of_birth']) {
                $errors['date_of_birth'] = ['The date_of_birth must be a valid date (YYYY-MM-DD).'];
            } elseif ($dob > new \DateTime()) {
                $errors['date_of_birth'] = ['The date_of_birth must not be in the future.'];
            }
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('first_name', $data)) {
            if (empty($data['first_name'])) {
                $errors['first_name'] = ['The first_name field cannot be empty.'];
            } elseif (strlen($data['first_name']) > 100) {
                $errors['first_name'] = ['The first_name may not be greater than 100 characters.'];
            }
        }

        if (array_key_exists('last_name', $data)) {
            if (empty($data['last_name'])) {
                $errors['last_name'] = ['The last_name field cannot be empty.'];
            } elseif (strlen($data['last_name']) > 100) {
                $errors['last_name'] = ['The last_name may not be greater than 100 characters.'];
            }
        }

        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['The email must be a valid email address.'];
        }

        if (!empty($data['mobile'])) {
            if (!ctype_digit($data['mobile'])) {
                $errors['mobile'] = ['The mobile must contain digits only.'];
            } elseif (strlen($data['mobile']) < 10) {
                $errors['mobile'] = ['The mobile must be at least 10 digits.'];
            }
        }

        if (!empty($data['gender']) && !in_array($data['gender'], self::VALID_GENDERS, true)) {
            $errors['gender'] = ['The gender must be one of: male, female, other.'];
        }

        if (!empty($data['date_of_birth'])) {
            $dob = \DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
            if (!$dob || $dob->format('Y-m-d') !== $data['date_of_birth']) {
                $errors['date_of_birth'] = ['The date_of_birth must be a valid date (YYYY-MM-DD).'];
            } elseif ($dob > new \DateTime()) {
                $errors['date_of_birth'] = ['The date_of_birth must not be in the future.'];
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive, suspended.'];
        }

        return $errors;
    }
}
