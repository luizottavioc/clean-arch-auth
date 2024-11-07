<?php

declare(strict_types=1);

namespace App\Application\Exceptions\Register;

use Exception;
use Throwable;

class RegistNumRegisteredException extends Exception
{
    public function __construct(string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}