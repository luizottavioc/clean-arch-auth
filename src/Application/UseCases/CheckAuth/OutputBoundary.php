<?php

declare(strict_types=1);

namespace App\Application\UseCases\CheckAuth;

use App\Domain\Entities\User;

class OutputBoundary
{
    public function __construct(private ?User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}