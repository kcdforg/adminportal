<?php

declare(strict_types=1);

namespace App\Modules\Community\Validators;

class InvitationValidator
{
    public function validateCreate(array $data): array
    {
        $errors = [];

        $hasMobile = !empty($data['invite_mobile']);
        $hasEmail  = !empty($data['invite_email']);

        if (!$hasMobile && !$hasEmail) {
            $errors['invite_mobile'] = ['At least one of invite_mobile or invite_email is required.'];
        }

        if ($hasMobile && !preg_match('/^\d{10,}$/', (string) $data['invite_mobile'])) {
            $errors['invite_mobile'] = ['The invite_mobile must contain digits only, minimum 10 digits.'];
        }

        if ($hasEmail && !filter_var($data['invite_email'], FILTER_VALIDATE_EMAIL)) {
            $errors['invite_email'] = ['The invite_email must be a valid email address.'];
        }

        return $errors;
    }

    public function validateAccept(array $data): array
    {
        $errors = [];

        if (empty($data['first_name'])) {
            $errors['first_name'] = ['The first_name field is required.'];
        } elseif (strlen((string) $data['first_name']) > 100) {
            $errors['first_name'] = ['The first_name must not exceed 100 characters.'];
        }

        if (empty($data['last_name'])) {
            $errors['last_name'] = ['The last_name field is required.'];
        } elseif (strlen((string) $data['last_name']) > 100) {
            $errors['last_name'] = ['The last_name must not exceed 100 characters.'];
        }

        if (empty($data['mobile'])) {
            $errors['mobile'] = ['The mobile field is required.'];
        } elseif (!preg_match('/^\d{10,}$/', (string) $data['mobile'])) {
            $errors['mobile'] = ['The mobile must contain digits only, minimum 10 digits.'];
        }

        if (empty($data['email'])) {
            $errors['email'] = ['The email field is required.'];
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['The email must be a valid email address.'];
        }

        if (empty($data['password'])) {
            $errors['password'] = ['The password field is required.'];
        } elseif (strlen((string) $data['password']) < 8) {
            $errors['password'] = ['The password must be at least 8 characters.'];
        }

        return $errors;
    }
}
