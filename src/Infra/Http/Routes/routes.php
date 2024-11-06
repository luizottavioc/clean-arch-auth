<?php

use App\Infra\Http\Conventions\Request;
use App\Infra\Http\Conventions\Response;

function route(string $controllerNamespace, string $action): void {
    try {
        if (!class_exists($controllerNamespace)) {
            $response = new Response(404, ['message' => 'Controller not found']);
            $response->handle();
        }

        $controllerInstance = new $controllerNamespace();

        if (!method_exists($controllerInstance, $action)) {
            $response = new Response(404, ['message' => 'Method not found']);
            $response->handle();
        }

        $request = new Request(
            $_SERVER['REQUEST_METHOD'],
            $_POST,
            $_SERVER
        );

        $response = $controllerInstance->$action($request);
        if (!$response instanceof Response) {
            $response = new Response(500, ['message' => 'Internal server error']);
        }

        $response->handle();

    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$router = [
    'POST' => [
        '/login' => fn() => route('App\Infra\Controllers\AuthController', 'handleLogin'),
        '/register' => fn() => route('App\Infra\Controllers\AuthController', 'handleRegister')
    ]
];