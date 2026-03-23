<?php

namespace App\Http\Middleware;

use Closure;

class Json
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request)
            ->header('Accept', 'application/json')
            ->header('Content-Type', 'application/json');

        // If the response is a redirect (validation error), convert to JSON
        if ($response->status() === 302 && $response->isRedirect()) {
            $errors = session()->get('errors');
            if ($errors && $errors->any()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors->getBag('default')->toArray()
                ], 422);
            }
        }

        return $response;
    }
}