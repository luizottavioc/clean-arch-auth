<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Factories;

use PHPUnit\Framework\TestCase;
use App\Infra\Http\Factories\CheckAuthControllerFactory;
use App\Infra\Http\Controllers\CheckAuthController;
use App\Infra\Database\DatabaseConnection;
use App\Infra\Repositories\PdoUserRepository;
use App\Infra\Adapters\PhpJwtAdapter;
use App\Infra\Http\Validators\CheckAuthControllerValidator;
use App\Application\UseCases\CheckAuth\CheckAuth;

class CheckAuthControllerFactoryTest extends TestCase
{
    private CheckAuthControllerFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new CheckAuthControllerFactory();
    }

    public function testCreateFactory(): void
    {
        $databaseMock = $this->createMock(DatabaseConnection::class);
        $databaseMock->method('getConnection')->willReturn($this->createMock(\PDO::class));

        $userRepositoryMock = $this->createMock(PdoUserRepository::class);
        $authTokenServiceMock = $this->createMock(PhpJwtAdapter::class);
        $controllerValidatorMock = $this->createMock(CheckAuthControllerValidator::class);

        $checkAuthUseCase = new CheckAuth($userRepositoryMock, $authTokenServiceMock);
        $controller = new CheckAuthController(
            $controllerValidatorMock,
            $checkAuthUseCase
        );

        $createdController = $this->factory->createFactory();

        $this->assertInstanceOf(CheckAuthController::class, $createdController);
    }
}
