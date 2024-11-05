<?php

declare(strict_types=1);

namespace App\Application\UseCases\Login;

final class OutputBoundary
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}