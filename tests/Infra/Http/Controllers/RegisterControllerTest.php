<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Controllers;

use App\Infra\Http\Controllers\RegisterController;
use App\Application\UseCases\Register\RegisterUser;
use App\Application\Exceptions\Register\EmailRegisteredException;
use App\Application\Exceptions\Register\RegistNumRegisteredException;
use App\Infra\Contracts\Request;
use App\Infra\Http\Conventions\Response as ResponseConvention;
use App\Infra\Http\Validators\RegisterControllerValidator;
use PHPUnit\Framework\TestCase;

class RegisterControllerTest extends TestCase
{
    private $validatorMock;
    private $useCaseMock;
    private $requestMock;

    protected function setUp(): void
    {
        $this->validatorMock = $this->createMock(RegisterControllerValidator::class);
        $this->useCaseMock = $this->createMock(RegisterUser::class);
        $this->requestMock = $this->createMock(Request::class);
    }

    public function testHandleSuccess(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'securepassword',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validateRegister')->willReturn([]);

        $outputBoundaryMock = $this->createMock(\App\Application\UseCases\Register\OutputBoundary::class);
        $outputBoundaryMock->method('getToken')->willReturn('valid_token');

        $this->useCaseMock->method('handle')->willReturn($outputBoundaryMock);

        $controller = new RegisterController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('User registered successfully', $response->getBody()['message']);
        $this->assertArrayHasKey('token', $response->getBody());
        $this->assertEquals('valid_token', $response->getBody()['token']);
    }

    public function testHandleEmailAlreadyRegistered(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'securepassword',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validateRegister')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new EmailRegisteredException('Email already registered'));

        $controller = new RegisterController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Email already registered', $response->getBody()['message']);
    }

    public function testHandleRegistNumAlreadyRegistered(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'securepassword',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validateRegister')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new RegistNumRegisteredException('Registration number already registered'));

        $controller = new RegisterController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(409, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Registration number already registered', $response->getBody()['message']);
    }

    public function testHandleValidationFailure(): void
    {
        $data = [
            'name' => '',
            'registration_number' => '',
            'email' => '',
            'password' => '',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validateRegister')->willReturn(['All fields are required']);

        $controller = new RegisterController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('errors', $response->getBody());
        $this->assertContains('All fields are required', $response->getBody()['errors']);
    }

    public function testHandleUnexpectedError(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'securepassword',
        ];

        $this->requestMock->method('getBody')->willReturn($data);

        $this->validatorMock->method('validateRegister')->willReturn([]);

        $this->useCaseMock->method('handle')->willThrowException(new \Exception('Unexpected error'));

        $controller = new RegisterController($this->validatorMock, $this->useCaseMock);
        $response = $controller->handle($this->requestMock);

        $this->assertInstanceOf(ResponseConvention::class, $response);
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('message', $response->getBody());
        $this->assertEquals('Unexpected error', $response->getBody()['message']);
    }
}
