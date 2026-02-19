<?php

namespace App\Http\Controllers\Lk\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\Lk\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{

    public function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            $user = new User;
            $user->phone = $request->phone;
        }
        
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->verification_code = $code;
        $user->verification_expires_at = now()->addMinutes(15);            
        $user->save();

        // todo send code to user

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent',
            'expires_in' => 300
        ]);
    }    

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)
                    ->where('verification_code', $request->code)
                    ->where('verification_expires_at', '>', now())
                    ->first();        

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->verification_code = null;
        $user->verification_expires_at = null;
        $user->save(); 
        
        $token = JWTAuth::fromUser($user);       

        return $this->respondWithToken($token);
    }   

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}