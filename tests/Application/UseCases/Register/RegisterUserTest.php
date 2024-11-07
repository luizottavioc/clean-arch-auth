<?php

declare(strict_types=1);

namespace Tests\Application\UseCases\Register;

use App\Application\UseCases\Register\RegisterUser;
use App\Application\Contracts\AuthTokenService;
use App\Domain\Repositories\UserRepository;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;
use App\Application\Exceptions\Register\EmailRegisteredException;
use App\Application\Exceptions\Register\RegistNumRegisteredException;
use App\Application\UseCases\Register\InputBoundary;
use App\Application\UseCases\Register\OutputBoundary;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class RegisterUserTest extends TestCase
{
    private RegisterUser $registerUser;
    private MockObject|UserRepository $userRepository;
    private MockObject|AuthTokenService $tokenService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->tokenService = $this->createMock(AuthTokenService::class);
        $this->registerUser = new RegisterUser($this->userRepository, $this->tokenService);
    }

    public function testHandleThrowsEmailRegisteredExceptionWhenEmailAlreadyExists(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('existing@example.com');

        $existingUser = $this->createMock(User::class);

        $this->userRepository
            ->method('findByEmail')
            ->with('existing@example.com')
            ->willReturn($existingUser);

        $this->expectException(EmailRegisteredException::class);
        $this->expectExceptionMessage('Email already registered');

        $this->registerUser->handle($input);
    }

    public function testHandleThrowsRegistNumRegisteredExceptionWhenRegistrationNumberAlreadyExists(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('new@example.com');
        $input->method('getRegistrationNumber')->willReturn('12345');

        $this->userRepository
            ->method('findByEmail')
            ->with('new@example.com')
            ->willReturn(null);

        $existingUserWithRegNumber = $this->createMock(User::class);

        $this->userRepository
            ->method('findByRegistrationNumber')
            ->with('12345')
            ->willReturn($existingUserWithRegNumber);

        $this->expectException(RegistNumRegisteredException::class);
        $this->expectExceptionMessage('Registration number already registered');

        $this->registerUser->handle($input);
    }

    public function testHandleSavesUserAndReturnsOutputBoundaryWithTokenWhenDataIsValid(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('newuser@example.com');
        $input->method('getRegistrationNumber')->willReturn('67890');
        $input->method('getName')->willReturn('New User');
        $input->method('getPassword')->willReturn('secure_password');

        $this->userRepository
            ->method('findByEmail')
            ->with('newuser@example.com')
            ->willReturn(null);

        $this->userRepository
            ->method('findByRegistrationNumber')
            ->with('67890')
            ->willReturn(null);

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(User::class));

        $token = 'generated_token';
        $this->tokenService
            ->method('generateUserToken')
            ->willReturn($token);

        $result = $this->registerUser->handle($input);

        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertSame($token, $result->getToken());
    }
}
