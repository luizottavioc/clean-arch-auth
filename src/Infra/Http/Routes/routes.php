<?php

use App\Infra\Contracts\Factory;
use App\Infra\Http\Conventions\Request;
use App\Infra\Http\Conventions\Response;

$router = [
    'POST' => [
        '/login' => fn() => route(
            'App\Infra\Http\Controllers\AuthController',
            'handleLogin',
            'App\Infra\Http\Factories\AuthControllerFactory'
        ),
        '/register' => fn() => route(
            'App\Infra\Http\Controllers\AuthController',
            'handleRegister',
            'App\Infra\Http\Factories\AuthControllerFactory'
        )
    ]
];

function route(string $controllerNamespace, string $action, string|null $factoryNamespace): void
{
    try {
        if (!class_exists($controllerNamespace)) {
            $response = new Response(404, ['message' => 'Controller not found']);
            $response->handle();
            return;
        }

        if ($factoryNamespace !== null && !class_exists($factoryNamespace)) {
            $response = new Response(404, ['message' => 'Factory not found']);
            $response->handle();
            return;
        }

        $factoryInstance = $factoryNamespace ? new $factoryNamespace() : null;

        if ($factoryInstance !== null && !$factoryInstance instanceof Factory) {
            $response = new Response(404, ['message' => 'Factory not found']);
            $response->handle();
            return;
        }

        $controllerInstance = $factoryInstance ? 
            new $controllerNamespace($factoryInstance->createFactory()) : 
            new $controllerNamespace();

        if (!method_exists($controllerInstance, $action)) {
            $response = new Response(404, ['message' => 'Method not found']);
            $response->handle();
            return;
        }

        $request = new Request(
            $_SERVER['REQUEST_METHOD'],
            $_POST,
            $_SERVER
        );

        $response = $controllerInstance->$action($request);
        if (!$response instanceof Response) {
            $response = new Response(500, ['message' => 'Internal server error']);
            return;
        }

        $response->handle();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}