<?php

declare(strict_types=1);

namespace App\Modules\Academics\DTOs;

class CreateProgramDTO
{
    public function __construct(
        public readonly string  $programName,
        public readonly string  $programType,
        public readonly float   $feeAmount,
        public readonly ?string $description = null,
        public readonly ?string $ageGroup    = null,
        public readonly string  $status      = 'active',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            programName:  $data['program_name'],
            programType:  $data['program_type'],
            feeAmount:    (float) $data['fee_amount'],
            description:  $data['description'] ?? null,
            ageGroup:     $data['age_group'] ?? null,
            status:       $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'program_name' => $this->programName,
            'program_type' => $this->programType,
            'fee_amount'   => $this->feeAmount,
            'description'  => $this->description,
            'age_group'    => $this->ageGroup,
            'status'       => $this->status,
        ];
    }
}
