<?php

declare(strict_types=1);

namespace App\Core\Exceptions;

class DuplicateException extends \RuntimeException
{
    public function __construct(string $message = 'Duplicate entry.')
    {
        parent::__construct($message, 409);
    }
}
