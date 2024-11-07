<?php

declare(strict_types=1);

namespace App\Application\UseCases\Register;

use App\Domain\Entities\User;

final class OutputBoundary
{
    public function __construct(private string $token)
    {
    }

    public function getToken(): string
    {
        return $this->token;
    }
}