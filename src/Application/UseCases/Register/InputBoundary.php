<?php

declare(strict_types=1);

namespace App\Application\UseCases\Register;

class InputBoundary
{
    private string $name;
    private string $registrationNumber;
    private string $email;
    private string $password;

    public function __construct(string $name, string $registrationNumber, string $email, string $password)
    {
        $this->name = $name;
        $this->registrationNumber = $registrationNumber;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}