<?php

declare(strict_types=1);

namespace App\Application\UseCases\CheckAuth;

use App\Application\Contracts\AuthTokenService;
use App\Domain\Repositories\UserRepository;

use App\Application\Exceptions\CheckAuth\InvalidTokenException;

final class CheckAuth
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthTokenService $tokenService
    ) {
    }

    public function handle(InputBoundary $input): OutputBoundary
    {
        $token = $input->getToken();
        
        $isValid = $this->tokenService->checkTokenIsValid($token);
        if (!$isValid) {
            throw new InvalidTokenException('Invalid token');
        }

        $userEmail = $this->tokenService->getUserEmailFromToken($token ?? '');
        $user = $this->userRepository->findByEmail($userEmail ?? '');

        if(empty($userEmail) || empty($user)) {
            throw new InvalidTokenException('Invalid token');
        }

        return new OutputBoundary( $user);
    }
}