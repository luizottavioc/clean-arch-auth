<?php

declare(strict_types=1);

namespace App\Application\UseCases\Login;

use App\Domain\Repositories\UserRepository;
use App\Application\Contracts\AuthTokenService;

use App\Application\Exceptions\Login\UserNotFoundException;
use App\Application\Exceptions\Login\WrongPasswordException;

final class LoginUser
{
    private UserRepository $userRepository;
    private AuthTokenService $tokenService;

    public function __construct(
        UserRepository $userRepository,
        AuthTokenService $tokenService
    ) {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
    }

    public function handle(InputBoundary $input): OutputBoundary
    {

        $inputEmail = $input->getEmail();
        $inputPassword = $input->getPassword();

        $user = $this->userRepository->findByEmail($inputEmail);

        if (empty($user)) {
            throw new UserNotFoundException('User not found');
        }

        $userPassword = $user->getPassword();
        $passwordMatch = $userPassword->isEqualTo($inputPassword);

        if (!$passwordMatch) {
            throw new WrongPasswordException('Password does not match');
        }

        $token = $this->tokenService->generateUserToken($user);

        return new OutputBoundary($token);
    }
}