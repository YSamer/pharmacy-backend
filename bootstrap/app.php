<?php

use App\Http\Middleware\HandleExceptions;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\HandleValidationErrors;
use App\Http\Middleware\LocalizationMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        apiPrefix: 'api/v1/',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            HandleValidationErrors::class,
            LocalizationMiddleware::class,
            HandleExceptions::class,
        ]);
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function ($exception, Request $request) {
            // For API requests
            if ($request->is('api/*')) {
                // Handle different types of exceptions
                if ($exception instanceof NotFoundHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Resource not found.',
                        'errors' => [
                            'detail' => $exception->getMessage()
                        ],
                    ], 404);
                }

                if ($exception instanceof UnauthorizedHttpException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access.',
                        'errors' => [
                            'detail' => $exception->getMessage()
                        ],
                    ], 401);
                }

                if ($exception instanceof ModelNotFoundException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The requested model could not be found.',
                        'errors' => [
                            'detail' => $exception->getMessage()
                        ],
                    ], 404);
                }

                if ($exception instanceof ValidationException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation errors occurred.',
                        'errors' => $exception->errors(),
                    ], 422);
                }

                // General fallback for any other unhandled exceptions
                return response()->json([
                    'success' => false,
                    'message' => 'An unexpected error occurred.',
                    'errors' => [
                        'detail' => $exception->getMessage()
                    ],
                ], 500);
            }
        });
    })->create();
