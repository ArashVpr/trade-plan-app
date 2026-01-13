<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
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
            // Handle Inertia requests
            if ($request->is('api/*') || $request->header('X-Inertia')) {
                // Return JSON errors for API and Inertia requests
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Don't expose sensitive error details in production
                $message = app()->environment('production')
                    ? 'Something went wrong. Please try again later.'
                    : $e->getMessage();

                return response()->json([
                    'message' => $message,
                ], 500);
            }

            // For web requests, let Laravel handle with custom error pages
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
