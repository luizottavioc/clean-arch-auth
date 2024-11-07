<?php

declare(strict_types=1);

namespace Tests\Application\UseCases\Login;

use App\Application\UseCases\Login\LoginUser;
use App\Application\Contracts\AuthTokenService;
use App\Domain\Repositories\UserRepository;
use App\Application\Exceptions\Login\UserNotFoundException;
use App\Application\Exceptions\Login\WrongPasswordException;
use App\Application\UseCases\Login\InputBoundary;
use App\Application\UseCases\Login\OutputBoundary;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\HashedPassword;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class LoginUserTest extends TestCase
{
    private LoginUser $loginUser;
    private MockObject|UserRepository $userRepository;
    private MockObject|AuthTokenService $tokenService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->tokenService = $this->createMock(AuthTokenService::class);
        $this->loginUser = new LoginUser($this->userRepository, $this->tokenService);
    }

    public function testHandleThrowsUserNotFoundExceptionWhenUserDoesNotExist(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('nonexistent@example.com');
        $input->method('getPassword')->willReturn('any_password');

        $this->userRepository
            ->method('findByEmail')
            ->with('nonexistent@example.com')
            ->willReturn(null);

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->loginUser->handle($input);
    }

    public function testHandleThrowsWrongPasswordExceptionWhenPasswordDoesNotMatch(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('user@example.com');
        $input->method('getPassword')->willReturn('wrong_password');

        $user = $this->createMock(User::class);
        $userPassword = $this->createMock(HashedPassword::class);
        $userPassword->method('isEqualTo')->with('wrong_password')->willReturn(false);

        $user->method('getPassword')->willReturn($userPassword);

        $this->userRepository
            ->method('findByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $this->expectException(WrongPasswordException::class);
        $this->expectExceptionMessage('Password does not match');

        $this->loginUser->handle($input);
    }

    public function testHandleReturnsOutputBoundaryWithTokenWhenCredentialsAreValid(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getEmail')->willReturn('user@example.com');
        $input->method('getPassword')->willReturn('correct_password');

        $user = $this->createMock(User::class);
        $userPassword = $this->createMock(HashedPassword::class);
        $userPassword->method('isEqualTo')->with('correct_password')->willReturn(true);

        $user->method('getPassword')->willReturn($userPassword);

        $this->userRepository
            ->method('findByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $token = 'sample_token';
        $this->tokenService
            ->method('generateUserToken')
            ->with($user)
            ->willReturn($token);

        $result = $this->loginUser->handle($input);

        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertSame($token, $result->getToken());
    }
}
