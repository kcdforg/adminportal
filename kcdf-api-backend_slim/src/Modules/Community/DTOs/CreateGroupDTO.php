<?php

declare(strict_types=1);

namespace App\Modules\Community\DTOs;

class CreateGroupDTO
{
    public function __construct(
        public readonly string  $groupName,
        public readonly ?string $description,
        public readonly string  $visibility,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            groupName:   (string) ($data['group_name'] ?? ''),
            description: isset($data['description']) && $data['description'] !== '' ? (string) $data['description'] : null,
            visibility:  (string) ($data['visibility'] ?? ''),
        );
    }
}
