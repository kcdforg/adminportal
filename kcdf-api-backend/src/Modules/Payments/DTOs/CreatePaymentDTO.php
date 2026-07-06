<?php

declare(strict_types=1);

namespace App\Modules\Payments\DTOs;

class CreatePaymentDTO
{
    public function __construct(
        public readonly int     $familyId,
        public readonly ?int    $enrollmentId,
        public readonly string  $paymentType,
        public readonly float   $amount,
        public readonly string  $paymentMethod,
        public readonly ?string $transactionReference,
        public readonly string  $status,
        public readonly ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            familyId:             (int) $data['family_id'],
            enrollmentId:         isset($data['enrollment_id']) && $data['enrollment_id'] !== null
                                      ? (int) $data['enrollment_id']
                                      : null,
            paymentType:          (string) $data['payment_type'],
            amount:               (float) $data['amount'],
            paymentMethod:        (string) $data['payment_method'],
            transactionReference: isset($data['transaction_reference']) && $data['transaction_reference'] !== ''
                                      ? (string) $data['transaction_reference']
                                      : null,
            status:               (string) $data['status'],
            notes:                isset($data['notes']) && $data['notes'] !== ''
                                      ? (string) $data['notes']
                                      : null,
        );
    }
}
