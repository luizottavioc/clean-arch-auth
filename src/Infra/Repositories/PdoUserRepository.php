<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Repositories\UserRepository;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;

use PDO;
use DateTimeImmutable;

class PdoUserRepository implements UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(User $user): void
    {
        $query = 'INSERT INTO users (name, registration_number, email, password) VALUES (:name, :registration_number, :email, :password)';
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'name' => $user->getName(),
            'registration_number' => $user->getRegistrationNumber(),
            'email' => $user->getEmail(),
            'password' => (string) $user->getPassword(),
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        $query = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetchObject();
        if($user === false) {
            return null;
        }

        $userInstance = new User(
            $user->name,
            $user->registration_number,
            $user->email,
            new HashedPassword($user->password, true),
            new DateTimeImmutable($user->created_at)
        );

        return $userInstance;
    }

    public function findByRegistrationNumber(string $registrationNumber): ?User
    {
        $query = 'SELECT * FROM users WHERE registration_number = :registration_number LIMIT 1';
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'registration_number' => $registrationNumber,
        ]);

        $user = $statement->fetchObject();
        if($user === false) {
            return null;
        }

        $userInstance = new User(
            $user->name,
            $user->registration_number,
            $user->email,
            new HashedPassword($user->password, true),
            new DateTimeImmutable($user->created_at)
        );

        return $userInstance;
    }
}