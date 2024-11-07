<?php

declare(strict_types=1);

namespace App\Infra\Adapters;

use App\Application\Contracts\AuthTokenService;
use App\Domain\Entities\User;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class PhpJwtAdapter implements AuthTokenService
{
    private string $jwtSecret;
    
    public function __construct()
    {
        if (empty($_ENV['DB_HOST'])) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../');
            $dotenv->safeLoad();
        }   

        $this->jwtSecret = $_ENV['JWT_SECRET'];
    }
    public function generateUserToken(User $user): string
    {
        $jwtPayload = [
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 3600,
            'sub' => $user->getEmail(),
        ];

        return JWT::encode($jwtPayload, $this->jwtSecret, 'HS256');
    }

    public function getDecodedToken(string $token): ?object
    {
        try {
            return JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
        } catch (\Throwable $th) {
            return null;
        }
    }

    public function getUserEmailFromToken(string $token): ?string
    {
        $decodedJwt = $this->getDecodedToken($token);
        if(empty($decodedJwt)) {
            return null;
        }

        return $decodedJwt->sub;
    }

    public function checkTokenIsValid(string $token): bool
    {
        $decodedJw = $this->getDecodedToken($token);
        if(empty($decodedJw)) {
            return false;
        }

        $exp = $decodedJw->exp;

        if($exp < time()) {
            return false;
        }
        
        return true;
    }
}