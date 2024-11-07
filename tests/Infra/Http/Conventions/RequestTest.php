<?php

declare(strict_types=1);

namespace Tests\Infra\Http\Conventions;

use App\Infra\Http\Conventions\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    private Request $request;

    protected function setUp(): void
    {
        $this->request = new Request(
            'GET',
            ['key' => 'value'],
            ['Authorization' => 'Bearer token']
        );
    }

    public function testGetMethod(): void
    {
        $this->assertEquals('GET', $this->request->getMethod());
    }

    public function testGetBody(): void
    {
        $this->assertEquals(['key' => 'value'], $this->request->getBody());
    }

    public function testGetHeader(): void
    {
        $this->assertEquals('Bearer token', $this->request->getHeader('Authorization'));
    }

    public function testGetHeaders(): void
    {
        $this->assertEquals(['Authorization' => 'Bearer token'], $this->request->getHeaders());
    }
}