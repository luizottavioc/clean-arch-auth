<?php

declare(strict_types=1);

namespace App\Infra\Contracts;

interface Request
{
    public function __construct(
        string $method,
        array $body,
        array $headers
    );
    
    public function getMethod(): string;
    public function getBody(): array;
    public function getHeader(string $name): string;
    public function getHeaders(): array;
}