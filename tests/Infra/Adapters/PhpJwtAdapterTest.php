<?php

declare(strict_types=1);

namespace Tests\Infra\Adapters;

use App\Domain\Entities\User;
use App\Infra\Adapters\PhpJwtAdapter;
use Firebase\JWT\JWT;
use PHPUnit\Framework\TestCase;

final class PhpJwtAdapterTest extends TestCase
{
    private PhpJwtAdapter $jwtAdapter;
    private string $jwtSecret = 'test_secret';

    protected function setUp(): void
    {
        $_ENV['JWT_SECRET'] = $this->jwtSecret;
        $this->jwtAdapter = new PhpJwtAdapter();
    }

    public function testGenerateUserTokenCreatesValidJwt(): void
    {
        $user = new User('John Doe', '12345', 'john.doe@example.com', $this->createMockHashedPassword());

        $token = $this->jwtAdapter->generateUserToken($user);
        $decoded = JWT::decode($token, new \Firebase\JWT\Key($this->jwtSecret, 'HS256'));

        $this->assertSame('http://localhost', $decoded->iss);
        $this->assertSame('john.doe@example.com', $decoded->sub);
        $this->assertTrue(isset($decoded->exp));
        $this->assertTrue($decoded->exp > time());
    }

    public function testGetUserEmailFromTokenReturnsCorrectEmail(): void
    {
        $user = new User('Jane Doe', '54321', 'jane.doe@example.com', $this->createMockHashedPassword());
        $token = $this->jwtAdapter->generateUserToken($user);

        $email = $this->jwtAdapter->getUserEmailFromToken($token);
        $this->assertSame('jane.doe@example.com', $email);
    }

    public function testCheckTokenIsValidReturnsTrueForValidToken(): void
    {
        $user = new User('Valid User', '67890', 'valid.user@example.com', $this->createMockHashedPassword());
        $token = $this->jwtAdapter->generateUserToken($user);

        $isValid = $this->jwtAdapter->checkTokenIsValid($token);
        $this->assertTrue($isValid);
    }

    public function testCheckTokenIsValidReturnsFalseForExpiredToken(): void
    {
        $expiredPayload = [
            'iss' => 'http://localhost',
            'aud' => 'http://localhost',
            'iat' => time() - 7200,
            'nbf' => time() - 7200,
            'exp' => time() - 3600,
            'sub' => 'expired.user@example.com',
        ];
        $token = JWT::encode($expiredPayload, $this->jwtSecret, 'HS256');

        $isValid = $this->jwtAdapter->checkTokenIsValid($token);
        $this->assertFalse($isValid);
    }

    public function testGetUserEmailFromTokenReturnsNullForInvalidToken(): void
    {
        $invalidToken = 'invalid.jwt.token';

        $email = $this->jwtAdapter->getUserEmailFromToken($invalidToken);
        $this->assertNull($email);
    }

    public function testCheckTokenIsValidReturnsFalseForInvalidToken(): void
    {
        $invalidToken = 'invalid.jwt.token';

        $isValid = $this->jwtAdapter->checkTokenIsValid($invalidToken);
        $this->assertFalse($isValid);
    }

    private function createMockHashedPassword(): \App\Domain\ValueObjects\HashedPassword
    {
        $mockPassword = $this->getMockBuilder(\App\Domain\ValueObjects\HashedPassword::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockPassword->method('getValue')->willReturn('hashed_password');
        return $mockPassword;
    }
}
