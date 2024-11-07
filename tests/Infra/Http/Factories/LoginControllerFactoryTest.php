<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Factories;

use PHPUnit\Framework\TestCase;
use App\Infra\Http\Factories\LoginControllerFactory;
use App\Infra\Http\Controllers\LoginController;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Validators\LoginControllerValidator;
use App\Application\UseCases\Login\LoginUser;

class LoginControllerFactoryTest extends TestCase
{
    private LoginControllerFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new LoginControllerFactory();
    }

    public function testCreateFactory(): void
    {
        $databaseMock = $this->createMock(DatabaseConnection::class);
        $databaseMock->method('getConnection')->willReturn($this->createMock(\PDO::class));

        $userRepositoryMock = $this->createMock(PdoUserRepository::class);
        $authTokenServiceMock = $this->createMock(PhpJwtAdapter::class);
        $controllerValidatorMock = $this->createMock(LoginControllerValidator::class);

        $loginUserUseCase = new LoginUser($userRepositoryMock, $authTokenServiceMock);
        new LoginController(
            $controllerValidatorMock,
            $loginUserUseCase
        );

        $createdController = $this->factory->createFactory();

        $this->assertInstanceOf(LoginController::class, $createdController);
    }
}
