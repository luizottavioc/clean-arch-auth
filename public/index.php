<?php

require __DIR__ . "../../vendor/autoload.php";
require __DIR__ . "../../src/Infra/Http/Routes/routes.php";
require __DIR__ . "../../src/Infra/Http/Conventions/Response.php";

use App\Infra\Http\Conventions\Response;

try {
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    if (empty($router[$requestMethod])) {
        $response = new Response(404, ['message' => 'Page not found']);
        $response->handle();
        return;
    }

    if (!array_key_exists($uri, $router[$requestMethod])) {
        $response = new Response(404, ['message' => 'Resource not found']);
        $response->handle();
        return;
    }

    $controller = $router[$requestMethod][$uri];
    $controller();
} catch(Exception $e){
    $response = new Response(500, ['message' => $e->getMessage()]);
    $response->handle();
}