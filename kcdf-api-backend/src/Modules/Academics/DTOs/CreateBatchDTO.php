<?php

declare(strict_types=1);

namespace App\Modules\Academics\DTOs;

class CreateBatchDTO
{
    public function __construct(
        public readonly int     $programId,
        public readonly string  $batchName,
        public readonly ?int    $capacity  = null,
        public readonly ?int    $trainerId = null,
        public readonly ?string $startDate = null,
        public readonly ?string $endDate   = null,
        public readonly string  $status    = 'upcoming',
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            programId: (int) $data['program_id'],
            batchName: $data['batch_name'],
            capacity:  isset($data['capacity']) ? (int) $data['capacity'] : null,
            trainerId: isset($data['trainer_id']) ? (int) $data['trainer_id'] : null,
            startDate: $data['start_date'] ?? null,
            endDate:   $data['end_date'] ?? null,
            status:    $data['status'] ?? 'upcoming',
        );
    }

    public function toArray(): array
    {
        return [
            'program_id' => $this->programId,
            'batch_name' => $this->batchName,
            'capacity'   => $this->capacity,
            'trainer_id' => $this->trainerId,
            'start_date' => $this->startDate,
            'end_date'   => $this->endDate,
            'status'     => $this->status,
        ];
    }
}
