<?php

use App\AppKernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

// Set a default error response
http_response_code(500);

require __DIR__.'/../vendor/autoload.php';

$readenv = function (string $variable, string $default): string {
    $value = $_ENV[$variable] ?? $_SERVER[$variable] ?? '';

    return $value !== '' ? $value : $default;
};

$env = $readenv('APP_ENV', 'dev');
$env = $env !== '' ? $env : 'dev';
$debug = filter_var($readenv('APP_DEBUG', 'dev' === $env ? '1' : '0'), FILTER_VALIDATE_BOOL);

if ($debug) {
    Debug::enable();
}

// Configure Symfony to trust header from CloudFront if request came from
// CloudFront, which is verified using a header added with a secret string.
(function (string $expected): void {
    if (!isset($_SERVER['HTTP_X_CLOUDFRONT_SECRET'])) {
        return;
    }

    $headerValue = (string) $_SERVER['HTTP_X_CLOUDFRONT_SECRET'];
    unset($_SERVER['HTTP_X_CLOUDFRONT_SECRET']); // Remove the header so Symfony doesn't see it.

    if ($expected === '' || $headerValue === '') {
        return;
    }

    if (hash_equals($expected, $headerValue)) {
        Request::setTrustedProxies(['REMOTE_ADDR'], Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_PROTO);
    }
})(
    $readenv('APP_CLOUDFRONT_SECRET', ''),
);

$kernel = new AppKernel($env, $debug);
unset($readenv, $env, $debug);

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
