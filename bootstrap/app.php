<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->trustProxies(
            at: '*',
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            // Handle validation exceptions specifically
            if ($e instanceof ValidationException) {
                // For Inertia requests, validation errors should be returned as validation response
                // Let Laravel's default handling take over - it will use Inertia's automatic error page
                return null;
            }

            // For API requests only, return JSON
            if ($request->is('api/*')) {
                $message = app()->environment('production')
                    ? 'Something went wrong. Please try again later.'
                    : $e->getMessage();

                return response()->json([
                    'message' => $message,
                ], 500);
            }

            // For all other requests (including Inertia), let Laravel handle with custom error pages
            return null;
        });

        // Report specific exceptions
        $exceptions->report(function (Throwable $e) {
            // Don't report validation exceptions
            if ($e instanceof ValidationException) {
                return false;
            }

            // Report other exceptions based on severity
            if ($e instanceof \Illuminate\Database\QueryException) {
                // Log database errors but don't report to external services
                logger()->error('Database error', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);

                return false;
            }

            // Report critical errors
            return true;
        });
    })->create();
