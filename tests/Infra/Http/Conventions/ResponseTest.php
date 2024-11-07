<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Conventions;

use App\Infra\Http\Conventions\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    private Response $response;

    protected function setUp(): void
    {
        $this->response = new Response(200, ['message' => 'success']);
    }

    public function testGetStatusCode(): void
    {
        $this->assertEquals(200, $this->response->getStatusCode());
    }

    public function testGetBody(): void
    {
        $this->assertEquals(['message' => 'success'], $this->response->getBody());
    }

    public function testHandle(): void
    {
        ob_start();
        $this->response->handle();
        $output = ob_get_clean();

        $expectedOutput = json_encode(['message' => 'success']);
        $this->assertEquals($expectedOutput, $output);
    }
}