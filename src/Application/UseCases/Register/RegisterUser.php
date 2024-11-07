<?php

declare(strict_types=1);

namespace App\Application\UseCases\Register;

use App\Domain\Entities\User;
use App\Domain\Repositories\UserRepository;
use App\Domain\ValueObjects\HashedPassword;

use App\Application\Contracts\AuthTokenService;
use App\Application\Exceptions\Register\EmailRegisteredException;
use App\Application\Exceptions\Register\RegistNumRegisteredException;


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

        if (!empty($userWithSameEmail)) {
            throw new EmailRegisteredException('Email already registered');
        }

        $userWithSameRegistrationNumber = $this->userRepository->findByRegistrationNumber($input->getRegistrationNumber());
        if (!empty($userWithSameRegistrationNumber)) {
            throw new RegistNumRegisteredException('Registration number already registered');
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