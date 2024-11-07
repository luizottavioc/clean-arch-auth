<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Controllers;

use App\Infra\Http\Controllers\LoginController;
use App\Application\UseCases\Login\LoginUser;
use App\Application\Exceptions\Login\UserNotFoundException;
use App\Application\Exceptions\Login\WrongPasswordException;
use App\Infra\Contracts\Request;
use App\Infra\Http\Conventions\Response as ResponseConvention;
use App\Infra\Http\Validator\LoginControllerValidator;
use PHPUnit\Framework\TestCase;

class LoginControllerTest extends TestCase
{
    private $validatorMock;
    private $useCaseMock;
    private $requestMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(LoginControllerValidator::class);
        $this->useCaseMock = $this->createMock(LoginUser::class);
        $this->requestMock = $this->createMock(Request::class);
    }

    public function testHandleSuccess(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'correct_password',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validate')->willReturn([]);

        $outputBoundaryMock = $this->createMock(\App\Application\UseCases\Login\OutputBoundary::class);
        $outputBoundaryMock->method('getToken')->willReturn('valid_token');

        $this->useCaseMock->method('handle')->willReturn($outputBoundaryMock);

        $controller = new LoginController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertArrayHasKey('token', $response->getBody());
        $this->assertEquals('valid_token', $response->getBody()['token']);
    }

    public function testHandleUserNotFound(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'incorrect_password',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validate')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new UserNotFoundException('User not found'));

        $controller = new LoginController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('User not found', $response->getBody()['message']);
    }

    public function testHandleWrongPassword(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'wrong_password',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validate')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new WrongPasswordException('Wrong password'));

        $controller = new LoginController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Wrong password', $response->getBody()['message']);
    }

    public function testHandleValidationFailure(): void
    {
        $data = [
            'email' => '',
            'password' => '',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validate')->willReturn(['Email and password are required']);

        $controller = new LoginController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getBody());
        $this->assertContains('Email and password are required', $response->getBody()['errors']);
    }

    public function testHandleUnexpectedError(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'correct_password',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validate')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new \Exception('Unexpected error'));

        $controller = new LoginController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Unexpected error', $response->getBody()['message']);
    }
}
