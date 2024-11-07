<?php

declare(strict_types=1);

namespace App\Infra\Http\Validators;

class RegisterControllerValidator
{
    public function validateRegister(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        if (empty($data['registration_number'])) {
            $errors[] = 'Registration number is required';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email is required';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password is required';
        }

        return $errors;
    }
}
