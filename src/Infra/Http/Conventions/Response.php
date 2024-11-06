<?php

declare(strict_types=1);

namespace App\Infra\Http\Conventions;

use App\Infra\Contracts\Response as ResponseInterface;

final class Response implements ResponseInterface
{
    public function __construct(
        public readonly int $statusCode,
        public readonly array $body
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getBody(): array
    {
        return $this->body;
    }

    public function handle(): void {
        http_response_code($this->statusCode);
        header('Content-Type: application/json');
        echo json_encode($this->body);
        exit;
    }
}