<?php

declare(strict_types=1);

namespace App\Modules\Notifications\DTOs;

class BroadcastNotificationDTO
{
    public function __construct(
        public readonly string $targetType,
        public readonly ?int   $targetId,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            targetType: (string) ($data['target_type'] ?? ''),
            targetId:   isset($data['target_id']) && $data['target_id'] !== '' ? (int) $data['target_id'] : null,
            title:      (string) ($data['title'] ?? ''),
            message:    (string) ($data['message'] ?? ''),
            type:       (string) ($data['type'] ?? ''),
        );
    }
}
