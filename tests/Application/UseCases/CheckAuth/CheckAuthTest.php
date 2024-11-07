<?php

declare(strict_types=1);

namespace Tests\Application\UseCases\CheckAuth;

use App\Application\UseCases\CheckAuth\CheckAuth;
use App\Application\Contracts\AuthTokenService;
use App\Domain\Repositories\UserRepository;
use App\Application\Exceptions\CheckAuth\InvalidTokenException;
use App\Application\UseCases\CheckAuth\InputBoundary;
use App\Application\UseCases\CheckAuth\OutputBoundary;
use App\Domain\Entities\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class CheckAuthTest extends TestCase
{
    private CheckAuth $checkAuth;
    private MockObject|UserRepository $userRepository;
    private MockObject|AuthTokenService $tokenService;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->tokenService = $this->createMock(AuthTokenService::class);
        $this->checkAuth = new CheckAuth($this->userRepository, $this->tokenService);
    }

    public function testHandleThrowsInvalidTokenExceptionWhenTokenIsInvalid(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getToken')->willReturn('invalid_token');

        $this->tokenService
            ->method('checkTokenIsValid')
            ->with('invalid_token')
            ->willReturn(false);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Invalid token');

        $this->checkAuth->handle($input);
    }

    public function testHandleThrowsInvalidTokenExceptionWhenUserNotFound(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getToken')->willReturn('valid_token');

        $this->tokenService
            ->method('checkTokenIsValid')
            ->with('valid_token')
            ->willReturn(true);

        $this->tokenService
            ->method('getUserEmailFromToken')
            ->with('valid_token')
            ->willReturn('user@example.com');

        $this->userRepository
            ->method('findByEmail')
            ->with('user@example.com')
            ->willReturn(null);

        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Invalid token');

        $this->checkAuth->handle($input);
    }

    public function testHandleReturnsOutputBoundaryWithUserWhenTokenAndUserAreValid(): void
    {
        $input = $this->createMock(InputBoundary::class);
        $input->method('getToken')->willReturn('valid_token');

        $this->tokenService
            ->method('checkTokenIsValid')
            ->with('valid_token')
            ->willReturn(true);

        $this->tokenService
            ->method('getUserEmailFromToken')
            ->with('valid_token')
            ->willReturn('user@example.com');

        $user = $this->createMock(User::class);

        $this->userRepository
            ->method('findByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $result = $this->checkAuth->handle($input);

        $this->assertInstanceOf(OutputBoundary::class, $result);
        $this->assertSame($user, $result->getUser());
    }
}
