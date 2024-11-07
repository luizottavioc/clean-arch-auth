<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

class HashedPassword
{
    private string $hashedPassword;

    public function __construct(string $password, bool $alreadyHashed = false)	
    {
        $this->hashedPassword = $alreadyHashed ? $password : self::hashPassword($password);
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