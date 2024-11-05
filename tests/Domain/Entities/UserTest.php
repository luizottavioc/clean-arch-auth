<?php

declare(strict_types=1);

namespace Tests\Domain\Entities;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;
use DateTimeImmutable;
use DateTimeInterface;

use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    private User $user;
    private string $name;
    private string $registrationNumber;
    private string $email;
    private HashedPassword $password;
    private DateTimeInterface $createdAt;

    protected function setUp(): void
    {
        $this->name = 'John Doe';
        $this->registrationNumber = '123456789';
        $this->email = 'john.doe@example.com';
        $this->password = new HashedPassword('secure_password_hash');
        $this->createdAt = new DateTimeImmutable();

        $this->user = new User(
            $this->name,
            $this->registrationNumber,
            $this->email,
            $this->password,
            $this->createdAt
        );
    }

    public function testCanGetName(): void
    {
        $this->assertSame($this->name, $this->user->getName());
    }

    public function testCanSetName(): void
    {
        $newName = 'Jane Doe';
        $this->user->setName($newName);
        $this->assertSame($newName, $this->user->getName());
    }

    public function testCanGetRegistrationNumber(): void
    {
        $this->assertSame($this->registrationNumber, $this->user->getRegistrationNumber());
    }

    public function testCanSetRegistrationNumber(): void
    {
        $newRegistrationNumber = '987654321';
        $this->user->setRegistrationNumber($newRegistrationNumber);
        $this->assertSame($newRegistrationNumber, $this->user->getRegistrationNumber());
    }

    public function testCanGetEmail(): void
    {
        $this->assertSame($this->email, $this->user->getEmail());
    }

    public function testCanSetEmail(): void
    {
        $newEmail = 'jane.doe@example.com';
        $this->user->setEmail($newEmail);
        $this->assertSame($newEmail, $this->user->getEmail());
    }

    public function testCanGetPassword(): void
    {
        $this->assertSame($this->password, $this->user->getPassword());
    }

    public function testCanSetPassword(): void
    {
        $this->user->setPassword('new_password_hash');
        $passwordIsNew = $this->user->getPassword()->isEqualTo('new_password_hash');
        $this->assertTrue($passwordIsNew);
    }

    public function testCanGetCreatedAt(): void
    {
        $this->assertSame($this->createdAt, $this->user->getCreatedAt());
    }

    public function testCanSetCreatedAt(): void
    {
        $newCreatedAt = new DateTimeImmutable('2023-01-01');
        $this->user->setCreatedAt($newCreatedAt);
        $this->assertSame($newCreatedAt, $this->user->getCreatedAt());
    }
}
