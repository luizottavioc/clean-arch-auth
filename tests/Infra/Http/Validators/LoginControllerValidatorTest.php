<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Validators;

use App\Infra\Http\Validators\LoginControllerValidator;
use PHPUnit\Framework\TestCase;

class LoginControllerValidatorTest extends TestCase
{
    private LoginControllerValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new LoginControllerValidator();
    }

    public function testValidateEmailMissing(): void
    {
        $data = [
            'email' => '',
            'password' => 'password123',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Email is required', $errors[0]);
    }

    public function testValidatePasswordMissing(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => '',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(1, $errors);
        $this->assertEquals('Password is required', $errors[0]);
    }

    public function testValidateEmailAndPasswordMissing(): void
    {
        $data = [
            'email' => '',
            'password' => '',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(2, $errors);
        $this->assertContains('Email is required', $errors);
        $this->assertContains('Password is required', $errors);
    }

    public function testValidateValidData(): void
    {
        $data = [
            'email' => 'user@example.com',
            'password' => 'password123',
        ];

        $errors = $this->validator->validate($data);

        $this->assertCount(0, $errors);
    }
}
