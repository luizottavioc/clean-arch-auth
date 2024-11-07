<?php

declare(strict_types=1);

namespace App\Application\UseCases\Login;

class OutputBoundary
{
    public function __construct(private string $token)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}