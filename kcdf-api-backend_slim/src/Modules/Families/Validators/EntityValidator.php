<?php

declare(strict_types=1);

namespace App\Modules\Families\Validators;

class EntityValidator
{
    private const VALID_ENTITY_TYPES = ['school', 'college', 'organization', 'hospital', 'association'];
    private const VALID_RELATION_TYPES = ['studies_at', 'works_at', 'member_of', 'volunteer_at'];
    private const VALID_STATUSES = ['active', 'inactive'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['entity_type'])) {
            $errors['entity_type'] = ['The entity_type field is required.'];
        } elseif (!in_array($data['entity_type'], self::VALID_ENTITY_TYPES, true)) {
            $errors['entity_type'] = ['The entity_type must be one of: school, college, organization, hospital, association.'];
        }

        if (empty($data['name'])) {
            $errors['name'] = ['The name field is required.'];
        } elseif (strlen($data['name']) > 255) {
            $errors['name'] = ['The name may not be greater than 255 characters.'];
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('entity_type', $data) && !in_array($data['entity_type'], self::VALID_ENTITY_TYPES, true)) {
            $errors['entity_type'] = ['The entity_type must be one of: school, college, organization, hospital, association.'];
        }

        if (array_key_exists('name', $data)) {
            if (empty($data['name'])) {
                $errors['name'] = ['The name field cannot be empty.'];
            } elseif (strlen($data['name']) > 255) {
                $errors['name'] = ['The name may not be greater than 255 characters.'];
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive.'];
        }

        return $errors;
    }

    public function validateRelation(array $data): array
    {
        $errors = [];

        if (empty($data['entity_id'])) {
            $errors['entity_id'] = ['The entity_id field is required.'];
        } elseif (!is_numeric($data['entity_id'])) {
            $errors['entity_id'] = ['The entity_id must be a valid integer.'];
        }

        if (empty($data['relation_type'])) {
            $errors['relation_type'] = ['The relation_type field is required.'];
        } elseif (!in_array($data['relation_type'], self::VALID_RELATION_TYPES, true)) {
            $errors['relation_type'] = ['The relation_type must be one of: studies_at, works_at, member_of, volunteer_at.'];
        }

        if (!empty($data['start_date'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['start_date']);
            if (!$date || $date->format('Y-m-d') !== $data['start_date']) {
                $errors['start_date'] = ['The start_date must be a valid date (YYYY-MM-DD).'];
            }
        }

        if (!empty($data['end_date'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $data['end_date']);
            if (!$date || $date->format('Y-m-d') !== $data['end_date']) {
                $errors['end_date'] = ['The end_date must be a valid date (YYYY-MM-DD).'];
            }
        }

        if (!empty($data['relation_context']) && !is_array($data['relation_context'])) {
            $errors['relation_context'] = ['The relation_context must be a valid JSON object.'];
        }

        return $errors;
    }
}
