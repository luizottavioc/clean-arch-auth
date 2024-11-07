<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Validators;

use App\Infra\Http\Validators\RegisterControllerValidator;
use PHPUnit\Framework\TestCase;

class RegisterControllerValidatorTest extends TestCase
{
    private RegisterControllerValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new RegisterControllerValidator();
    }

    public function testValidateNameMissing(): void
    {
        $data = [
            'name' => '',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Name is required', $errors[0]);
    }

    public function testValidateRegistrationNumberMissing(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '',
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Registration number is required', $errors[0]);
    }

    public function testValidateEmailMissing(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => '',
            'password' => 'password123',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Email is required', $errors[0]);
    }

    public function testValidatePasswordMissing(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => '',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Password is required', $errors[0]);
    }

    public function testValidateAllFieldsMissing(): void
    {
        $data = [
            'name' => '',
            'registration_number' => '',
            'email' => '',
            'password' => '',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(4, $errors);
        $this->assertContains('Name is required', $errors);
        $this->assertContains('Registration number is required', $errors);
        $this->assertContains('Email is required', $errors);
        $this->assertContains('Password is required', $errors);
    }

    public function testValidateValidData(): void
    {
        $data = [
            'name' => 'John Doe',
            'registration_number' => '123456',
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        $errors = $this->validator->validateRegister($data);

        $this->assertCount(0, $errors);
    }
}
