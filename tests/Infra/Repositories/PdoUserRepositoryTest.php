<?php

declare(strict_types=1);

namespace Tests\Infra\Repositories;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;
use App\Infra\Repositories\PdoUserRepository;
use DateTimeImmutable;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

final class PdoUserRepositoryTest extends TestCase
{
    private PDO $pdo;
    private PdoUserRepository $userRepository;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('
            CREATE TABLE users (
                id INTEGER PRIMARY KEY,
                name TEXT,
                registration_number TEXT,
                email TEXT UNIQUE,
                password TEXT,
                created_at TEXT DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $this->userRepository = new PdoUserRepository($this->pdo);
    }

    public function testSaveStoresUserInDatabase(): void
    {
        $user = $this->createSampleUser();

        $this->userRepository->save($user);

        $statement = $this->pdo->query('SELECT COUNT(*) FROM users WHERE email = "jane.doe@example.com"');
        $this->assertSame(1, (int) $statement->fetchColumn());
    }

    public function testFindByEmailReturnsUserIfExists(): void
    {
        $user = $this->createSampleUser();
        $this->userRepository->save($user);

        $foundUser = $this->userRepository->findByEmail($user->getEmail());

        $this->assertNotNull($foundUser);
        $this->assertSame($user->getName(), $foundUser->getName());
        $this->assertSame($user->getEmail(), $foundUser->getEmail());
    }

    public function testFindByEmailReturnsNullIfUserDoesNotExist(): void
    {
        $foundUser = $this->userRepository->findByEmail('nonexistent@example.com');

        $this->assertNull($foundUser);
    }

    public function testFindByRegistrationNumberReturnsUserIfExists(): void
    {
        $user = $this->createSampleUser();
        $this->userRepository->save($user);

        $foundUser = $this->userRepository->findByRegistrationNumber($user->getRegistrationNumber());

        $this->assertNotNull($foundUser);
        $this->assertSame($user->getName(), $foundUser->getName());
        $this->assertSame($user->getRegistrationNumber(), $foundUser->getRegistrationNumber());
    }

    public function testFindByRegistrationNumberReturnsNullIfUserDoesNotExist(): void
    {
        $foundUser = $this->userRepository->findByRegistrationNumber('99999');

        $this->assertNull($foundUser);
    }

    public function testSaveThrowsExceptionForDuplicateEmail(): void
    {
        $user = $this->createSampleUser();
        $this->userRepository->save($user);

        $this->expectException(PDOException::class);

        $duplicateUser = new User(
            'John Smith',
            '98765',
            'jane.doe@example.com',
            new HashedPassword('anotherPassword'),
            new DateTimeImmutable()
        );

        $this->userRepository->save($duplicateUser);
    }

    private function createSampleUser(): User
    {
        return new User(
            'Jane Doe',
            '12345',
            'jane.doe@example.com',
            new HashedPassword('password123'),
            new DateTimeImmutable()
        );
    }
}
