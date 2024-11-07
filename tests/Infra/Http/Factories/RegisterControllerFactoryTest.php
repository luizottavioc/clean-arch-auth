<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Factories;

use PHPUnit\Framework\TestCase;
use App\Infra\Http\Factories\RegisterControllerFactory;
use App\Infra\Http\Controllers\RegisterController;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Validators\RegisterControllerValidator;
use App\Application\UseCases\Register\RegisterUser;

class RegisterControllerFactoryTest extends TestCase
{
    private RegisterControllerFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new RegisterControllerFactory();
    }

    public function testCreateFactory(): void
    {
        $databaseMock = $this->createMock(DatabaseConnection::class);
        $databaseMock->method('getConnection')->willReturn($this->createMock(\PDO::class));

        $userRepositoryMock = $this->createMock(PdoUserRepository::class);
        $authTokenServiceMock = $this->createMock(PhpJwtAdapter::class);
        $controllerValidatorMock = $this->createMock(RegisterControllerValidator::class);

        $registerUserUseCase = new RegisterUser($userRepositoryMock, $authTokenServiceMock);

        new RegisterController(
            $controllerValidatorMock,
            $registerUserUseCase
        );

        $createdController = $this->factory->createFactory();

        $this->assertInstanceOf(RegisterController::class, $createdController);
    }
}
