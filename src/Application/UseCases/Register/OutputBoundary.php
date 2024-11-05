<?php

declare(strict_types=1);

namespace App\Application\UseCases\Register;

final class OutputBoundary
{
    private string $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }
}