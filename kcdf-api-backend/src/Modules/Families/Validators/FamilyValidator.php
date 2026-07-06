<?php

declare(strict_types=1);

namespace App\Modules\Families\Validators;

class FamilyValidator
{
    private const VALID_ROLES = ['primary', 'normal', 'student'];
    private const VALID_RELATIONSHIPS = ['father', 'mother', 'guardian', 'child'];
    private const VALID_STATUSES = ['active', 'inactive'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        if (empty($data['family_name'])) {
            $errors['family_name'] = ['The family_name field is required.'];
        } elseif (strlen($data['family_name']) > 255) {
            $errors['family_name'] = ['The family_name may not be greater than 255 characters.'];
        }

        if (!empty($data['address'])) {
            $errors = array_merge($errors, $this->validateAddress($data['address']));
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (array_key_exists('family_name', $data)) {
            if (empty($data['family_name'])) {
                $errors['family_name'] = ['The family_name field cannot be empty.'];
            } elseif (strlen($data['family_name']) > 255) {
                $errors['family_name'] = ['The family_name may not be greater than 255 characters.'];
            }
        }

        if (!empty($data['status']) && !in_array($data['status'], self::VALID_STATUSES, true)) {
            $errors['status'] = ['The status must be one of: active, inactive.'];
        }

        if (!empty($data['address'])) {
            $errors = array_merge($errors, $this->validateAddress($data['address']));
        }

        return $errors;
    }

    public function validateAddMember(array $data): array
    {
        $errors = [];

        if (empty($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id field is required.'];
        } elseif (!is_numeric($data['profile_id'])) {
            $errors['profile_id'] = ['The profile_id must be a valid integer.'];
        }

        if (empty($data['relationship_type'])) {
            $errors['relationship_type'] = ['The relationship_type field is required.'];
        } elseif (!in_array($data['relationship_type'], self::VALID_RELATIONSHIPS, true)) {
            $errors['relationship_type'] = ['The relationship_type must be one of: father, mother, guardian, child.'];
        }

        if (empty($data['member_role'])) {
            $errors['member_role'] = ['The member_role field is required.'];
        } elseif (!in_array($data['member_role'], self::VALID_ROLES, true)) {
            $errors['member_role'] = ['The member_role must be one of: primary, normal, student.'];
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
