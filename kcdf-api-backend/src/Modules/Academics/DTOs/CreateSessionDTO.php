<?php

declare(strict_types=1);

namespace App\Modules\Academics\DTOs;

class CreateSessionDTO
{
    public function __construct(
        public readonly int     $batchId,
        public readonly string  $sessionDate,
        public readonly string  $sessionType,
        public readonly ?int    $sessionNumber = null,
        public readonly ?string $sessionTitle  = null,
        public readonly ?string $startTime     = null,
        public readonly ?string $endTime       = null,
        public readonly ?int    $trainerId     = null,
        public readonly string  $status        = 'scheduled',
    ) {}

    public static function fromArray(int $batchId, array $data): self
    {
        return new self(
            batchId:       $batchId,
            sessionDate:   $data['session_date'],
            sessionType:   $data['session_type'],
            sessionNumber: isset($data['session_number']) ? (int) $data['session_number'] : null,
            sessionTitle:  $data['session_title'] ?? null,
            startTime:     $data['start_time'] ?? null,
            endTime:       $data['end_time'] ?? null,
            trainerId:     isset($data['trainer_id']) ? (int) $data['trainer_id'] : null,
            status:        $data['status'] ?? 'scheduled',
        );
    }

    public function toArray(): array
    {
        return [
            'batch_id'       => $this->batchId,
            'session_date'   => $this->sessionDate,
            'session_type'   => $this->sessionType,
            'session_number' => $this->sessionNumber,
            'session_title'  => $this->sessionTitle,
            'start_time'     => $this->startTime,
            'end_time'       => $this->endTime,
            'trainer_id'     => $this->trainerId,
            'status'         => $this->status,
        ];
    }
}
