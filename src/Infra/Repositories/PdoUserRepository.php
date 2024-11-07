<?php

declare(strict_types=1);

namespace App\Infra\Repositories;

use App\Domain\Repositories\UserRepository;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;

use PDO;
use DateTimeImmutable;
use Exception;

final class PdoUserRepository implements UserRepository
{

    public function __construct(private PDO $pdo)
    {
    }

    public function save(User $user): void
    {
        $query = 'INSERT INTO users (name, email, password) VALUES (:name, :email, :password)';
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'password' => (string) $user->getPassword(),
        ]);
    }

    public function findByEmail(string $email): User
    {
        $query = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            'email' => $email,
        ]);

        $user = $statement->fetchObject();
        if($user === false) {
            throw new Exception('User not found');
        }

        $userInstance = new User(
            $user->name,
            $user->registration_number,
            $user->email,
            new HashedPassword($user->password),
            new DateTimeImmutable($user->created_at)
        );

        return $userInstance;
    }
}