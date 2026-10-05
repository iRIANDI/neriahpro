<?php

namespace App\Exceptions;

use RuntimeException;

class SecurityException extends RuntimeException
{
    public function __construct(string $message = 'Security policy violation detected.', int $code = 403, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
