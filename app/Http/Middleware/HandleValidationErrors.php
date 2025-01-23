<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class HandleValidationErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($response->exception && $response->exception instanceof \Illuminate\Validation\ValidationException) {
            $errors = $response->exception->errors();
            $firstErrorMessage = reset($errors);
            $errorMessage = reset($firstErrorMessage);

            return response()->json([
                'message' => $errorMessage,
            ], 422);
        }

        return $response;
    }
}