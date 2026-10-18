<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class BusinessRuleException extends \RuntimeException
{
    private string $errorCode;

    public function __construct(string $errorCode, string $message = 'Business rule violation.')
    {
        parent::__construct($message, 422);
        $this->errorCode = $errorCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
