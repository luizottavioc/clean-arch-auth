<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Validators;

use App\Infra\Http\Validators\CheckAuthControllerValidator;
use PHPUnit\Framework\TestCase;

class CheckAuthControllerValidatorTest extends TestCase
{
    private CheckAuthControllerValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CheckAuthControllerValidator();
    }

    public function testValidateAuthorizationHeaderMissing(): void
    {
        $headers = [];

        $errors = $this->validator->validate($headers);

        $this->assertCount(1, $errors);
        $this->assertEquals('Header Authorization is required', $errors[0]);
    }

    public function testValidateTokenMissing(): void
    {
        $headers = [
            'Authorization' => 'Bearer ',
        ];

        $errors = $this->validator->validate($headers);

        $this->assertCount(1, $errors);
        $this->assertEquals('Token is required', $errors[0]);
    }

    public function testValidateValidToken(): void
    {
        $headers = [
            'Authorization' => 'Bearer valid_token',
        ];

        $errors = $this->validator->validate($headers);

        $this->assertCount(0, $errors);
    }
}
