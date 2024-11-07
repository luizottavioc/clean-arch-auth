<?php

declare(strict_types=1);

namespace App\Infra\Http\Validator;

class CheckAuthControllerValidator
{
    public function validate(array $headers): array
    {
        $errors = [];

        if (empty($headers['Authorization'])) {
            $errors[] = 'Header Authorization is required';
            return $errors;
        }

        $headerAuthorization = $headers['Authorization'];
        $token = str_replace('Bearer ', '', $headerAuthorization);

        if (empty($token)) {
            $errors[] = 'Token is required';
        }

        return $errors;
    }
}
