<?php

namespace App\Http\Middleware;

use Closure;

class PhoneVerified
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
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }        

        if (!$user->phone_verified_at) {
            return response()->json(['error' => 'Not verified']);
        }

        return $next($request);
    }
}