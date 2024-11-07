<?php

declare(strict_types=1);

namespace App\Infra\Http\Validators;

class LoginControllerValidator
{
    public function validate(array $data): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
