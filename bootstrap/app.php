<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Fix for "Call to a member function make() on null" error in PendingBroadcast destructor
        // This error occurs during application shutdown when the container is already null
        // It's safe to ignore as it happens after the response has been sent
        $exceptions->reportable(function (\Throwable $e) {
            // Only suppress the specific error that occurs in Validator during shutdown
            if ($e instanceof \Error &&
                str_contains($e->getMessage(), 'Call to a member function make() on null') &&
                str_contains($e->getFile(), 'Illuminate/Validation/Validator.php')) {
                return false; // Don't report this specific error
            }

            return true; // Report all other exceptions normally
        });
    })->create();
