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
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token is Invalid'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token is Expired'], 401);
            } else {
                return response()->json(['error' => 'Authorization Token not found'], 401);
            }
        }        
        
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->verification_code = $code;
        $user->verification_expires_at = now()->addMinutes(15);            
        $user->save();

        // todo send code to user

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent',
            'expires_in' => 900
        ]);
    }

    public function acceptCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($user->verification_code != $request->code || $user->verification_expires_at <= now()) {
            return response()->json(['error' => 'Wrong code']);
        }

        $user->verification_code = null;
        $user->verification_expires_at = null;
        $user->phone_verified_at = now();
        $user->save(); 

        return response()->json(['status' => 'Success']);
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
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('phone', $request->phone)->first();        

        if (!$user) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $credentials = request(['phone', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }      

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('phone', $request->phone)->first();        

        if ($user) {
            return response()->json(['error' => 'User already exists']);
        }

        $user = new User;
        $user->phone = $request->phone;
        $user->password = Hash::make($request->password);

        $user->save();

        //auth()->loginUsingId($user->id);

        $token = JWTAuth::fromUser($user);       

        return $this->respondWithToken($token);        

    }     

    public function refresh()
    {
        try {
            $token = JWTAuth::parseToken();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token is Invalid'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token is Expired'], 401);
            } else {
                return response()->json(['error' => 'Authorization Token not found'], 401);
            }
        }            

        return $this->respondWithToken(JWTAuth::refresh($token));
    } 
    
    /**
     * Logout and invalidate current token
     */
    public function logout()
    {

        try {
            $token = JWTAuth::parseToken();

            JWTAuth::invalidate($token);

            auth()->logout();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token is Invalid'], 401);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token is Expired'], 401);
            } else {
                return response()->json(['error' => 'Authorization Token not found'], 401);
            }
        }

        return response()->json(['status' => 'Success']);
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