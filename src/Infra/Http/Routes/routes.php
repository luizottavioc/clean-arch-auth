<?php

use App\Infra\Contracts\Factory;
use App\Infra\Http\Conventions\Request;
use App\Infra\Http\Conventions\Response;

$router = [
    'POST' => [
        '/login' => fn() => route(
            'App\Infra\Http\Controllers\LoginController',
            'handle',
            'App\Infra\Http\Factories\LoginControllerFactory'
        ),
        '/register' => fn() => route(
            'App\Infra\Http\Controllers\RegisterController',
            'handle',
            'App\Infra\Http\Factories\RegisterControllerFactory'
        )
    ]
];

function route(string $controllerNamespace, string $action, string|null $factoryNamespace): void
{
    try {
        $controller = resolveController($controllerNamespace, $factoryNamespace);

        if (!$controller || !method_exists($controller, $action)) {
            sendResponse(404, 'Controller or method not found');
            return;
        }

        $request = new Request(
            $_SERVER['REQUEST_METHOD'],
            json_decode(file_get_contents('php://input'), true),
            getallheaders()
        );

        $response = $controller->$action($request);

        if (!$response instanceof Response) {
            sendResponse(500, 'Internal server error');
            return;
        }

        $response->handle();
    } catch (Exception $e) {
        sendResponse(500, $e->getMessage());
    }
}

function resolveController(string $controllerNamespace, ?string $factoryNamespace): ?object
{
    if (!class_exists($controllerNamespace)) {
        sendResponse(404, 'Controller not found');
        return null;
    }

    if ($factoryNamespace !== null) {
        if (!class_exists($factoryNamespace) || !is_subclass_of($factoryNamespace, Factory::class)) {
            sendResponse(404, 'Factory not found or invalid');
            return null;
        }

        $factoryInstance = new $factoryNamespace();
        return $factoryInstance->createFactory();
    }

    return new $controllerNamespace();
}

function sendResponse(int $statusCode, string $message): void
{
    $response = new Response($statusCode, ['message' => $message]);
    $response->handle();
}
