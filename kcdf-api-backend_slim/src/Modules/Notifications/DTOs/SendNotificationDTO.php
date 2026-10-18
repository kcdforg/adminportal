<?php

declare(strict_types=1);

namespace App\Modules\Notifications\DTOs;

class SendNotificationDTO
{
    public function __construct(
        public readonly array  $memberIds,
        public readonly string $title,
        public readonly string $message,
        public readonly string $type,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            memberIds: array_map('intval', (array) ($data['member_ids'] ?? [])),
            title:     (string) ($data['title'] ?? ''),
            message:   (string) ($data['message'] ?? ''),
            type:      (string) ($data['type'] ?? ''),
        );
    }
}
