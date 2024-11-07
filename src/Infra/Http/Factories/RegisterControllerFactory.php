<?php

declare(strict_types=1);

namespace App\Infra\Http\Factories;

use App\Infra\Contracts\Factory;

use App\Application\UseCases\Login\LoginUser;
use App\Application\UseCases\Register\RegisterUser;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Http\Validator\RegisterControllerValidator;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Controllers\RegisterController;

final class RegisterControllerFactory implements Factory
{
    public function createFactory(): object
    {
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $userRepository = new PdoUserRepository($connection);
        $authTokenService = new PhpJwtAdapter();
        
        $controllerValidator = new RegisterControllerValidator();
        $registerUserUseCase = new RegisterUser($userRepository, $authTokenService);

        return new RegisterController(
            $controllerValidator,
            $registerUserUseCase,
        );
    }
}