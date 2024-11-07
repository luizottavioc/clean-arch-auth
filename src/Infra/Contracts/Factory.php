<?php

declare(strict_types=1);

namespace App\Infra\Contracts;

interface Factory
{
    public function createFactory(): object;
}