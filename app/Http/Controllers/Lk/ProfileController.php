<?php

namespace App\Http\Controllers\Lk;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use JWTAuth;

class ProfileController extends Controller
{
    public function profile()
    {
        try {
            // Method 1: Using JWTAuth facade
            $user = JWTAuth::parseToken()->authenticate();

            $token = auth()->refresh();
            
            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => [
		            'access_token' => $token,
		            'token_type' => 'bearer',
		            'expires_in' => auth()->factory()->getTTL() * 60
		        ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'User not found'
            ], 404);
        }
    }
}