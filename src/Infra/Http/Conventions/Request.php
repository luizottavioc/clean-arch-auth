<?php

declare(strict_types=1);

namespace App\Infra\Http\Conventions;

use App\Infra\Contracts\Request as RequestInterface;

class Request implements RequestInterface
{
    public function __construct(
        public readonly string $method,
        public readonly array $body,
        public readonly array $headers
    ) {
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function getHeader(string $name): string
    {
        return $this->headers[$name];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}