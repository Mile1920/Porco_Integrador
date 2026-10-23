<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(\App\Http\Middleware\AutoLogout::class);
        $middleware->validateCsrfTokens(except: [
            'api/sensor/esp32',
            'api/sensor/esp32/recibir',
            'sensors/data',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
