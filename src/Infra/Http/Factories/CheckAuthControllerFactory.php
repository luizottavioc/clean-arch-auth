<?php

declare(strict_types=1);

namespace App\Infra\Http\Factories;

use App\Infra\Contracts\Factory;

use App\Application\UseCases\CheckAuth\CheckAuth;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Http\Validator\CheckAuthControllerValidator;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Controllers\CheckAuthController;

final class CheckAuthControllerFactory implements Factory
{
    public function createFactory(): object
    {
        $database = new DatabaseConnection();
        $connection = $database->getConnection();

        $userRepository = new PdoUserRepository($connection);
        $authTokenService = new PhpJwtAdapter();

        $controllerValidator = new CheckAuthControllerValidator();
        $checkAuthUseCase = new CheckAuth($userRepository, $authTokenService);

        return new CheckAuthController(
            $controllerValidator,
            $checkAuthUseCase,
        );
    }
}