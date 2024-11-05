<?php

declare(strict_types=1);

namespace App\Application\Contracts;

use App\Domain\Entities\User;

interface AuthTokenService
{
    public function generateUserToken(User $user): string;
}