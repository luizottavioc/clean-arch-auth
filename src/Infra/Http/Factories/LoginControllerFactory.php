<?php

declare(strict_types=1);

namespace App\Infra\Http\Factories;

use App\Infra\Contracts\Factory;

use App\Application\UseCases\Login\LoginUser;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Http\Validator\LoginControllerValidator;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Controllers\LoginController;

class LoginControllerFactory implements Factory
{
    public function createFactory(): object
    {
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $userRepository = new PdoUserRepository($connection);
        $authTokenService = new PhpJwtAdapter();
        
        $controllerValidator = new LoginControllerValidator();
        $loginUserUseCase = new LoginUser($userRepository, $authTokenService);

        return new LoginController(
            $controllerValidator,
            $loginUserUseCase,
        );
    }
}