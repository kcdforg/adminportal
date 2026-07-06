<?php

declare(strict_types=1);

namespace App\Modules\Academics\DTOs;

class CreateEnrollmentDTO
{
    public function __construct(
        public readonly int $familyId,
        public readonly int $memberId,
        public readonly int $batchId,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            familyId: (int) $data['family_id'],
            memberId: (int) $data['member_id'],
            batchId:  (int) $data['batch_id'],
        );
    }
}
