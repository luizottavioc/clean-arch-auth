<?php

declare(strict_types=1);

namespace App\Application\UseCases\Register;

use App\Application\Exceptions\Register\EmailAlreadyRegisteredException;
use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Application\Contracts\AuthTokenService;

use App\Domain\ValueObjects\HashedPassword;
use Exception;

final class RegisterUser
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
        $userWithSameEmail = $this->userRepository->findByEmail($inputEmail);

        if ($userWithSameEmail !== null) {
            throw new EmailAlreadyRegisteredException('Email already registered');
        }

        $hashedPassword = new HashedPassword($input->getPassword());
        $user = new User(
            $input->getName(),
            $input->getRegistrationNumber(),
            $input->getEmail(),
            $hashedPassword,
        );
        
        $this->userRepository->save($user);
        
        $token = $this->tokenService->generateUserToken($user);

        return new OutputBoundary($token);
    }
}