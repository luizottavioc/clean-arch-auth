<?php

declare(strict_types=1);

namespace App\Infra\Contracts;

interface Response
{
    public function __construct(
        int $statusCode,
        array $body
    );
    
    public function getStatusCode(): int;
    public function getBody(): array;
    public function handle(): void;
}