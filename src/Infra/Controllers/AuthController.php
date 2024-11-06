<?php

declare(strict_types=1);

namespace App\Infra\Controllers;

use App\Infra\Contracts\Request;
use App\Infra\Http\Conventions\Response;

final class AuthController
{
    public function handleLogin(Request $request): Response
    {
        return new Response(200, ['message' => 'Login successful']);
    }

    public function handleRegister(Request $request): Response
    {
        return new Response(200, ['message' => 'Register successful']);
    }
}