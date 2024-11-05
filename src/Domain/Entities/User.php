<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\HashedPassword;
use DateTimeImmutable;
use DateTimeInterface;

final class User
{
    private string $name;
    private string $registrationNumber;
    private string $email;
    private HashedPassword $password;
    private DateTimeInterface $createdAt;

    public function __construct(
        string $name,
        string $registrationNumber,
        string $email,
        HashedPassword $password,
        DateTimeInterface $createdAt = new DateTimeImmutable()
    ) {
        $this->name = $name;
        $this->registrationNumber = $registrationNumber;
        $this->email = $email;
        $this->password = $password;
        $this->createdAt = $createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function setRegistrationNumber(string $registrationNumber): void
    {
        $this->registrationNumber = $registrationNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): HashedPassword
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = new HashedPassword($password);
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
