<?php

declare(strict_types=1);

namespace App\Modules\Payments\Validators;

class PaymentValidator
{
    private const PAYMENT_TYPES   = ['class_fee', 'donation', 'event_fee', 'refund'];
    private const PAYMENT_METHODS = ['cash', 'bank_transfer', 'upi', 'card', 'cheque', 'online'];
    private const STATUSES        = ['pending', 'completed', 'failed'];

    public function validateCreate(array $data): array
    {
        $errors = [];

        // family_id
        if (empty($data['family_id'])) {
            $errors['family_id'] = ['The family_id field is required.'];
        } elseif (!is_numeric($data['family_id'])) {
            $errors['family_id'] = ['The family_id must be a valid integer.'];
        }

        // payment_type
        if (empty($data['payment_type'])) {
            $errors['payment_type'] = ['The payment_type field is required.'];
        } elseif (!in_array($data['payment_type'], self::PAYMENT_TYPES, true)) {
            $errors['payment_type'] = [
                'The payment_type must be one of: ' . implode(', ', self::PAYMENT_TYPES) . '.',
            ];
        }

        // enrollment_id — required only for class_fee
        $paymentType = $data['payment_type'] ?? '';
        if ($paymentType === 'class_fee') {
            if (empty($data['enrollment_id'])) {
                $errors['enrollment_id'] = ['The enrollment_id is required for class_fee payments.'];
            } elseif (!is_numeric($data['enrollment_id'])) {
                $errors['enrollment_id'] = ['The enrollment_id must be a valid integer.'];
            }
        } elseif (!empty($data['enrollment_id']) && !is_numeric($data['enrollment_id'])) {
            $errors['enrollment_id'] = ['The enrollment_id must be a valid integer.'];
        }

        // amount
        if (!isset($data['amount']) || $data['amount'] === '') {
            $errors['amount'] = ['The amount field is required.'];
        } elseif (!is_numeric($data['amount'])) {
            $errors['amount'] = ['The amount must be a numeric value.'];
        } elseif ((float) $data['amount'] < 0.01) {
            $errors['amount'] = ['The amount must be at least 0.01.'];
        } elseif ($this->hasMoreThanTwoDecimalPlaces($data['amount'])) {
            $errors['amount'] = ['The amount must not exceed 2 decimal places.'];
        }

        // payment_method
        if (empty($data['payment_method'])) {
            $errors['payment_method'] = ['The payment_method field is required.'];
        } elseif (!in_array($data['payment_method'], self::PAYMENT_METHODS, true)) {
            $errors['payment_method'] = [
                'The payment_method must be one of: ' . implode(', ', self::PAYMENT_METHODS) . '.',
            ];
        }

        // transaction_reference — required when payment_method is not cash
        $method = $data['payment_method'] ?? '';
        if ($method !== 'cash' && in_array($method, self::PAYMENT_METHODS, true) && empty($data['transaction_reference'])) {
            $errors['transaction_reference'] = ['The transaction_reference is required for non-cash payments.'];
        }

        // status
        if (empty($data['status'])) {
            $errors['status'] = ['The status field is required.'];
        } elseif (!in_array($data['status'], self::STATUSES, true)) {
            $errors['status'] = ['The status must be one of: ' . implode(', ', self::STATUSES) . '.'];
        }

        // notes
        if (!empty($data['notes']) && strlen((string) $data['notes']) > 1000) {
            $errors['notes'] = ['The notes must not exceed 1000 characters.'];
        }

        return $errors;
    }

    public function validateUpdate(array $data): array
    {
        $errors = [];

        if (isset($data['status']) && !in_array($data['status'], self::STATUSES, true)) {
            $errors['status'] = ['The status must be one of: ' . implode(', ', self::STATUSES) . '.'];
        }

        if (isset($data['notes']) && strlen((string) $data['notes']) > 1000) {
            $errors['notes'] = ['The notes must not exceed 1000 characters.'];
        }

        return $errors;
    }

    private function hasMoreThanTwoDecimalPlaces(mixed $value): bool
    {
        $str    = (string) $value;
        $dotPos = strpos($str, '.');

        if ($dotPos === false) {
            return false;
        }

        return strlen($str) - $dotPos - 1 > 2;
    }
}
