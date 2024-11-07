<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Controllers;

use App\Infra\Http\Controllers\CheckAuthController;
use App\Application\Exceptions\CheckAuth\InvalidTokenException;
use App\Application\UseCases\CheckAuth\CheckAuth;
use App\Infra\Contracts\Request;
use App\Infra\Http\Conventions\Response as ResponseConvention;
use App\Infra\Http\Validators\CheckAuthControllerValidator;
use PHPUnit\Framework\TestCase;

class CheckAuthControllerTest extends TestCase
{
    private $validatorMock;
    private $useCaseMock;
    private $requestMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(CheckAuthControllerValidator::class);
        $this->useCaseMock = $this->createMock(CheckAuth::class);
        $this->requestMock = $this->createMock(Request::class);
    }

    public function testHandleSuccess(): void
    {
        $headers = [
            'Authorization' => 'Bearer valid_token',
        ];

        $this->requestMock->method('getHeaders')->willReturn($headers);

        $this->validatorMock->method('validate')->willReturn([]);

        $outputBoundaryMock = $this->createMock(\App\Application\UseCases\CheckAuth\OutputBoundary::class);
        $outputBoundaryMock->method('getUser')->willReturn($this->createMock(\App\Domain\Entities\User::class));

        $this->useCaseMock->method('handle')->willReturn($outputBoundaryMock);

        $controller = new CheckAuthController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('isAuthenticated', $response->getBody());
        $this->assertTrue($response->getBody()['isAuthenticated']);
    }

    public function testHandleInvalidToken(): void
    {
        $headers = [
            'Authorization' => 'Bearer invalid_token',
        ];

        $this->requestMock->method('getHeaders')->willReturn($headers);

        $this->validatorMock->method('validate')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new InvalidTokenException('Invalid token'));

        $controller = new CheckAuthController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('isAuthenticated', $response->getBody());
        $this->assertFalse($response->getBody()['isAuthenticated']);
    }

    public function testHandleValidationFailure(): void
    {
        $headers = [
            'Authorization' => '',
        ];

        $this->requestMock->method('getHeaders')->willReturn($headers);

        $this->validatorMock->method('validate')->willReturn(['Authorization header is missing']);

        $controller = new CheckAuthController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getBody());
        $this->assertContains('Authorization header is missing', $response->getBody()['errors']);
    }

    public function testHandleUnexpectedError(): void
    {
        $headers = [
            'Authorization' => 'Bearer valid_token',
        ];

        $this->requestMock->method('getHeaders')->willReturn($headers);

        $this->validatorMock->method('validate')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new \Exception('Unexpected error'));

        $controller = new CheckAuthController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Unexpected error', $response->getBody()['message']);
    }
}
