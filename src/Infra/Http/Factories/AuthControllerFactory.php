<?php

declare(strict_types=1);

namespace App\Infra\Http\Factories;

use App\Infra\Contracts\Factory;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Register\RegisterUser;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Http\Validator\AuthControllerValidator;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Controllers\AuthController;

final class AuthControllerFactory implements Factory
{
    public function createFactory(): object
    {
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $userRepository = new PdoUserRepository($connection);
        $authTokenService = new PhpJwtAdapter();
        $authValidator = new AuthControllerValidator();

        $loginUserUseCase = new LoginUser($userRepository, $authTokenService);
        $registerUserUseCase = new RegisterUser($userRepository, $authTokenService);

        return new AuthController(
            $authValidator,
            $loginUserUseCase,
            $registerUserUseCase,
        );
    }
}