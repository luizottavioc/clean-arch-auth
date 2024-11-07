<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

final class HashedPassword
{
    private string $hashedPassword;

    public function __construct(string $originalPassword)
    {
        $this->hashedPassword = self::hashPassword($originalPassword);
    }

    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function getValue(): string
    {
        return $this->hashedPassword;
    }

    public function isEqualTo(string $originalPassword): bool
    {
        return password_verify($originalPassword, $this->hashedPassword);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}