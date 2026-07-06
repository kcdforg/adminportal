<?php

declare(strict_types=1);

namespace App\Modules\Notifications\Validators;

class NotificationValidator
{
    private const NOTIFICATION_TYPES = ['in_app', 'push', 'email'];
    private const TARGET_TYPES       = ['batch', 'group', 'all_families'];

    public function validateSend(array $data): array
    {
        $errors = [];

        if (!isset($data['member_ids']) || !is_array($data['member_ids']) || empty($data['member_ids'])) {
            $errors['member_ids'] = ['The member_ids field is required and must be a non-empty array.'];
        } else {
            foreach ($data['member_ids'] as $id) {
                if (!is_numeric($id) || (int) $id <= 0) {
                    $errors['member_ids'] = ['All member_ids must be valid positive integers.'];
                    break;
                }
            }
        }

        $errors = array_merge($errors, $this->validateCommonFields($data));

        return $errors;
    }

    public function validateBroadcast(array $data): array
    {
        $errors = [];

        if (empty($data['target_type'])) {
            $errors['target_type'] = ['The target_type field is required.'];
        } elseif (!in_array($data['target_type'], self::TARGET_TYPES, true)) {
            $errors['target_type'] = ['The target_type must be one of: ' . implode(', ', self::TARGET_TYPES) . '.'];
        }

        $targetType = $data['target_type'] ?? '';
        if (in_array($targetType, ['batch', 'group'], true) && empty($data['target_id'])) {
            $errors['target_id'] = ['The target_id is required when target_type is batch or group.'];
        }

        $errors = array_merge($errors, $this->validateCommonFields($data));

        return $errors;
    }

    private function validateCommonFields(array $data): array
    {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = ['The title field is required.'];
        } elseif (strlen((string) $data['title']) > 255) {
            $errors['title'] = ['The title must not exceed 255 characters.'];
        }

        if (empty($data['message'])) {
            $errors['message'] = ['The message field is required.'];
        } elseif (strlen((string) $data['message']) > 2000) {
            $errors['message'] = ['The message must not exceed 2000 characters.'];
        }

        if (empty($data['type'])) {
            $errors['type'] = ['The type field is required.'];
        } elseif (!in_array($data['type'], self::NOTIFICATION_TYPES, true)) {
            $errors['type'] = ['The type must be one of: ' . implode(', ', self::NOTIFICATION_TYPES) . '.'];
        }

        return $errors;
    }
}
