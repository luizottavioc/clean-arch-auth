<?php

declare(strict_types=1);

namespace App\Infra\Http\Factories;

use App\Infra\Contracts\Factory;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Register\RegisterUser;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Http\Controllers\AuthController;
use App\Infra\Repositories\PdoUserRepository;

final class AuthControllerFactory implements Factory
{
    public function createFactory(): object
    {
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $userRepository = new PdoUserRepository($connection);
        $authTokenService = new AuthTokenService();

        $loginUserUseCase = new LoginUser($userRepository, $authTokenService);
        $registerUserUseCase = new RegisterUser($userRepository, $authTokenService);

        return new AuthController(
            $loginUserUseCase,
            $registerUserUseCase
        );
    }
}