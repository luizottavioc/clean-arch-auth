<?php

declare(strict_types=1);

namespace Tests\Domain\ValueObjects;

use App\Domain\ValueObjects\HashedPassword;
use PHPUnit\Framework\TestCase;

final class HashedPasswordTest extends TestCase
{
    private HashedPassword $hashedPassword;
    private string $originalPassword;

    protected function setUp(): void
    {
        $this->originalPassword = 'secure_password';
        $this->hashedPassword = new HashedPassword($this->originalPassword);
    }

    public function testHashingCreatesHashedPassword(): void
    {
        $hashedValue = $this->hashedPassword->getValue();
        $this->assertNotSame(
            $this->originalPassword,
            $hashedValue,
            'The hashed password should not be the same as the original password.'
        );
        $this->assertTrue(
            password_verify($this->originalPassword, $hashedValue),
            'The hashed password should verify correctly with the original password.'
        );
    }

    public function testGetValueReturnsHashedPassword(): void
    {
        $this->assertIsString(
            $this->hashedPassword->getValue(),
            'The hashed password should be a string.'
        );
    }

    public function testIsEqualToReturnsTrueForCorrectPassword(): void
    {
        $this->assertTrue(
            $this->hashedPassword->isEqualTo($this->originalPassword),
            'The isEqualTo method should return true for the correct original password.'
        );
    }

    public function testIsEqualToReturnsFalseForIncorrectPassword(): void
    {
        $incorrectPassword = 'wrong_password';
        $this->assertFalse(
            $this->hashedPassword->isEqualTo($incorrectPassword),
            'The isEqualTo method should return false for an incorrect password.'
        );
    }

    public function testToStringReturnsHashedPassword(): void
    {
        $this->assertSame(
            $this->hashedPassword->getValue(),
            (string) $this->hashedPassword,
            'The __toString method should return the hashed password value.'
        );
    }

    public function testHashPasswordGeneratesUniqueHashes(): void
    {
        $anotherPassword = 'another_secure_password';
        $anotherHashedPassword = HashedPassword::hashPassword($anotherPassword);
        $this->assertNotSame(
            $this->hashedPassword->getValue(),
            $anotherHashedPassword,
            'Hashing different passwords should generate different hashes.'
        );
    }
}
