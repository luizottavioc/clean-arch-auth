<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Application\Contracts\AuthTokenService;
use App\Domain\Entities\User;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;

final class PhpJwtAdapter implements AuthTokenService
{
    public function generateUserToken(User $user): string
    {
        if (empty($_ENV['DB_HOST'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            $dotenv->safeLoad();
        }

        $jwtSecret = $_ENV['JWT_SECRET'];
        $jwtPayload = [
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'sub' => $user->getName(),
        ];

        return JWT::encode($jwtPayload, $jwtSecret, 'HS256');
    }
}