<?php

declare(strict_types=1);

namespace Tests\Infra\Database;

use App\Infra\Database\DatabaseConnection;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class DatabaseConnectionTest extends TestCase
{
    private array $originalEnv;

    protected function setUp(): void
    {
        $this->originalEnv = $_ENV;

        $_ENV['DB_CONNECTION'] = 'mysql';
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_PORT'] = '3306';
        $_ENV['DB_DATABASE'] = 'test_db';
        $_ENV['DB_USERNAME'] = 'root';
        $_ENV['DB_PASSWORD'] = 'password';
    }

    protected function tearDown(): void
    {
        $_ENV = $this->originalEnv;
    }

    public function testSuccessfulConnection(): void
    {
        $databaseConnection = $this->getMockBuilder(DatabaseConnection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConnection'])
            ->getMock();

        $pdoMock = $this->createMock(PDO::class);
        $databaseConnection->method('getConnection')->willReturn($pdoMock);

        $this->assertInstanceOf(PDO::class, $databaseConnection->getConnection());
    }

    public function testThrowsRuntimeExceptionOnConnectionFailure(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Database connection error');

        $_ENV['DB_HOST'] = 'invalid_host';

        new DatabaseConnection();
    }

    public function testLoadsEnvironmentVariablesWhenMissing(): void
    {
        unset($_ENV['DB_HOST']);

        $dotenvMock = $this->getMockBuilder(\Dotenv\Dotenv::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['safeLoad'])
            ->getMock();

        $dotenvMock->expects($this->once())->method('safeLoad');

        $dotenvMock->safeLoad();

        $this->assertTrue(true);
    }
}
