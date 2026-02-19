<?php
// app/Http/Middleware/JwtMiddleware.php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    public function handle($request, Closure $next, $expectedGuard)
    {
        try {
            // This is the key line - it parses the token from the request
            // and authenticates the user
            $token = JWTAuth::parseToken();

            /*
            
            $payload = $token->getPayload();

            $prvHash = $payload->get('prv');

            $expectedProvider = config("auth.guards.{$expectedGuard}.provider");
            
            // Generate the same hash that jwt-auth uses
            // In jwt-auth, it uses sha256 to hash the provider name
            $expectedHash = hash('sha256', $expectedProvider);
            
            if ($prvHash !== $expectedHash) {
                return response()->json([
                    'error' => 'Invalid token for this guard',
                    'message' => 'Provider mismatch',
                    'debug' => [
                        'expected_guard' => $expectedGuard,
                        'expected_provider' => $expectedProvider,
                        'expected_hash' => $expectedHash,
                        'token_prv_hash' => $prvHash
                    ]
                ], 401);
            }

            */

            $user = $token->authenticate();
            
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token is Invalid'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token is Expired'], 401);
            } else {
                return response()->json(['error' => 'Authorization Token not found'], 401);
            }
        }
        
        return $next($request);
    }   
}