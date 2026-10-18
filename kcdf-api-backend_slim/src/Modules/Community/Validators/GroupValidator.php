<?php

declare(strict_types=1);

namespace App\Modules\Community\Validators;

class GroupValidator
{
    private const VISIBILITIES = ['public', 'private', 'invite_only'];
    private const STATUSES     = ['active', 'inactive', 'archived'];
    private const ACTIONS      = ['ban', 'remove'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['group_name'])) {
            $errors['group_name'] = ['The group_name field is required.'];
        } elseif (strlen((string) $data['group_name']) > 255) {
            $errors['group_name'] = ['The group_name must not exceed 255 characters.'];
        }

        if (empty($data['visibility'])) {
            $errors['visibility'] = ['The visibility field is required.'];
        } elseif (!in_array($data['visibility'], self::VISIBILITIES, true)) {
            $errors['visibility'] = ['The visibility must be one of: ' . implode(', ', self::VISIBILITIES) . '.'];
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['group_name']) && strlen((string) $data['group_name']) > 255) {
            $errors['group_name'] = ['The group_name must not exceed 255 characters.'];
        }

        if (isset($data['visibility']) && !in_array($data['visibility'], self::VISIBILITIES, true)) {
            $errors['visibility'] = ['The visibility must be one of: ' . implode(', ', self::VISIBILITIES) . '.'];
        }

        if (isset($data['status']) && !in_array($data['status'], self::STATUSES, true)) {
            $errors['status'] = ['The status must be one of: ' . implode(', ', self::STATUSES) . '.'];
        }

        return $errors;
    }

    public function validateMemberAction(array $data): array
    {
        $errors = [];

        if (empty($data['action'])) {
            $errors['action'] = ['The action field is required.'];
        } elseif (!in_array($data['action'], self::ACTIONS, true)) {
            $errors['action'] = ['The action must be one of: ' . implode(', ', self::ACTIONS) . '.'];
        }

        return $errors;
    }
}
