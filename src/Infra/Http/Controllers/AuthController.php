<?php

declare(strict_types=1);

namespace App\Infra\Http\Controllers;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Register\RegisterUser;
use App\Infra\Contracts\Request;
use App\Infra\Contracts\Response;

use App\Infra\Http\Conventions\Response as ResponseConvention;

final class AuthController
{
    public function __construct(
        private LoginUser $useCaseLoginUser,
        private RegisterUser $useCaseRegisterUser
    ) {}
    
    public function handleLogin(Request $request): Response
    {
        return new ResponseConvention(200, ['message' => 'Login successful']);
    }

    public function handleRegister(Request $request): Response
    {
        return new ResponseConvention(200, ['message' => 'Register successful']);
    }
}