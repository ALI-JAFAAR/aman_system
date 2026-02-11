<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',

        then: function () {
            require(__DIR__ .'/../routes/app.php');
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CORS for SPA + API
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);

        // Sanctum SPA auth: make API stateful when request comes from allowed domains.
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
