<?php

use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// Set a default error response
// http_response_code(500);

require __DIR__.'/../vendor/autoload.php';

$env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'dev';
$debug = filter_var($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? ('dev' === $env), FILTER_VALIDATE_BOOL);

if ($debug) {
    // Debug::enable();
}

$kernel = new AppKernel($env, $debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
