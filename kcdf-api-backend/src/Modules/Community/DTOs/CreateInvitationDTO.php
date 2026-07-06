<?php

declare(strict_types=1);

namespace App\Modules\Community\DTOs;

class CreateInvitationDTO
{
    public function __construct(
        public readonly ?string $inviteMobile,
        public readonly ?string $inviteEmail,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            inviteMobile: isset($data['invite_mobile']) && $data['invite_mobile'] !== '' ? (string) $data['invite_mobile'] : null,
            inviteEmail:  isset($data['invite_email'])  && $data['invite_email'] !== ''  ? (string) $data['invite_email']  : null,
        );
    }
}
