<?php

namespace App\Http\Controllers\Lk\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use JWTAuth;
use App\Models\Lk\User;
use App\Models\Common\Option;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{

    public function sendCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }                
        
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->verification_code = $code;
        $user->verification_expires_at = now()->addMinutes(15);            
        $user->save();

        // todo send code to user

        return response()->json([
            'status' => 'success',
            'message' => 'Verification code sent',
            'expires_in' => 900
        ]);
    }

    public function acceptCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->verification_code != $request->code || $user->verification_expires_at <= now()) {
            return response()->json(['error' => 'Wrong code']);
        }

        $user->verification_code = null;
        $user->verification_expires_at = null;
        //$user->phone_verified_at = now();
        $user->save(); 

        $token = JWTAuth::fromUser($user);       

        return $this->respondWithToken($token);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'phone' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($user->verification_code != $request->code || $user->verification_expires_at <= now()) {
            return response()->json(['error' => 'Wrong code']);
        }

        $user->verification_code = null;
        $user->verification_expires_at = null;
        //$user->phone_verified_at = now();
        $user->password = Hash::make($request->password);
        $user->save(); 

        //$token = JWTAuth::fromUser($user);       

        //return $this->respondWithToken($token);

        return response()->json(['status' => 'success']);
    }               

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('login', $request->login)->first();        

        if (!$user) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $credentials = request(['login', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }      

        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string|min:8'
        ]);

        $user = User::where('login', $request->login)->first();        

        if ($user) {
            return response()->json(['error' => 'User already exists']);
        }

        $user = new User;
        $user->login = $request->login;
        $user->password = Hash::make($request->password);

        $user->save();

        //auth()->loginUsingId($user->id);

        $token = JWTAuth::fromUser($user);       

        return $this->respondWithToken($token);        

    }

    public function register_phone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();        

        if ($user) {
            return response()->json(['error' => 'User already exists']);
        }

        $user = new User;
        $user->phone = $request->phone;

        $user->save();

        return response()->json(['status' => 'success']);       

    }

    public function login_phone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $user = User::where('phone', $request->phone)->first();        

        if (!$user) {
            return response()->json(['error' => 'Not found'], 404);
        }

        $user->verification_code = null;
        $user->verification_expires_at = null;
        $user->save();

        return response()->json(['status' => 'success']);
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

    public function get_vk_client_id(Request $request) {
        $client_id = Option::where('slug', 'vk_client_id')->first()->value;

        return response()->json(['status' => 'Success', 'client_id' => $client_id]);
    }

    public function get_vk_code_challenge(Request $request) {
        $vk_state = Option::where('slug', 'vk_state')->first()->value;

        $sha256Hash = hash('sha256', $vk_state, true);
        $base64Encoded = base64_encode($sha256Hash);

        $code_challenge = str_replace(['+', '/', '='], ['-', '_', ''], $base64Encoded);

        return response()->json(['status' => 'Success', 'code_challenge' => $code_challenge]);
    }

    public function get_ya_client_id(Request $request) {
        $client_id = Option::where('slug', 'ya_client_id')->first()->value;

        return response()->json(['status' => 'Success', 'client_id' => $client_id]);
    }        

    public function vkAuth(Request $request) {
        $vk_state = Option::where('slug', 'vk_state')->first()->value;
        $client_id = Option::where('slug', 'vk_client_id')->first()->value;

        $app_url = Option::where('slug', 'app_url')->first()->value;

        $url = 'https://id.vk.ru/oauth2/auth';
        $param = [
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
            'code_verifier' => $vk_state,
            'client_id' => $client_id,
            'device_id' => $request->input('device_id'),
            'redirect_uri' => route('lk.auth.vk_auth'),
            'state' => $request->input('device_id')
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));

        $out = curl_exec($curl);
        $json = json_decode($out, true);

        if (empty($json) || empty($json['access_token'])) {
            return redirect()->away($app_url . '?error=Ошибка авторизации');
        }

        $url = 'https://id.vk.ru/oauth2/user_info';
        $param = [
            'access_token' => $json['access_token'],
            'client_id' => $client_id,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));

        $out = curl_exec($curl);
        $json = json_decode($out, true);

        if (empty($json) || empty($json['user'])) {
            return redirect()->away($app_url . '?error=Ошибка получения данных пользователя');
        }

        $vk_id = $json['user']['user_id'];

        $user = User::where('vk_id', $vk_id)->first();

        if (empty($user)) {
            $user = new User;
            $user->vk_id = $vk_id;
        }

        $user->email = $json['user']['email'];
        $user->name = $json['user']['last_name'] . ' ' . $json['user']['first_name'];        

        if (empty($user->phone)) {

            if (empty($json['user']['phone'])) {

                $user->save();

                $token = JWTAuth::fromUser($user);

                return redirect()->away($app_url . '?need_phone=1')->withCookie('access_token', $token, 60);
            }

            $exist_user = User::where('phone', $json['user']['phone'])->first();

            if (!empty($exist_user)) {

                $exist_user->vk_id = $vk_id;
                $exist_user->email = $json['user']['email'];
                $exist_user->name = $json['user']['last_name'] . ' ' . $json['user']['first_name'];
                $exist_user->save(); 

                $token = JWTAuth::fromUser($exist_user);

            } else {
                $user->phone = $json['user']['phone'];
                $user->save();
                $token = JWTAuth::fromUser($user);
            }

        } else {
            $user->save(); 
            $token = JWTAuth::fromUser($user);           
        }

        return redirect()->away($app_url)->withCookie('access_token', $token, 60);                            
    }

    public function yandexAuth(Request $request) {
        $client_id = Option::where('slug', 'ya_client_id')->first()->value;
        $client_secret = Option::where('slug', 'ya_client_secret')->first()->value;

        $app_url = Option::where('slug', 'app_url')->first()->value;

        $url = 'https://oauth.yandex.ru/token';
        $param = [
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
        ];

        $header_auth = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode($client_id . ':' . $client_secret),
        ];        

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header_auth);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($param));

        $out = curl_exec($curl);
        $json = json_decode($out, true);

        if (empty($json) || empty($json['access_token'])) {
            return redirect()->away($app_url . '?error=Ошибка авторизации');
        }

        $url = 'https://login.yandex.ru/info';

         $header_auth = [
            'Authorization: OAuth ' . $json['access_token'],
        ];       

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, false);

        $out = curl_exec($curl);
        $json = json_decode($out, true);

        if (empty($json) || empty($json['id'])) {
            return redirect()->away($app_url . '?error=Ошибка получения данных пользователя');
        }

        $ya_id = $json['id'];

        $user = User::where('ya_id', $ya_id)->first();

        if (empty($user)) {
            $user = new User;
            $user->ya_id = $ya_id;
        }

        $user->email = $json['default_email'];
        $user->name = $json['last_name'] . ' ' . $json['first_name'];        

        if (empty($user->phone)) {

            if (empty($json['default_phone']['number'])) {

                $user->save();

                $token = JWTAuth::fromUser($user);

                return redirect()->away($app_url . '?need_phone=1')->withCookie('access_token', $token, 60);
            }

            $exist_user = User::where('phone', $json['default_phone']['number'])->first();

            if (!empty($exist_user)) {

                $exist_user->vk_id = $vk_id;
                $exist_user->email = $json['default_email'];
                $exist_user->name = $json['last_name'] . ' ' . $json['first_name'];
                $exist_user->save(); 

                $token = JWTAuth::fromUser($exist_user);

            } else {
                $user->phone = $json['default_phone']['number'];
                $user->save();
                $token = JWTAuth::fromUser($user);
            }

        } else {
            $user->save(); 
            $token = JWTAuth::fromUser($user);           
        }

        return redirect()->away($app_url)->withCookie('access_token', $token, 60);                             
    }    

    public function savePhone(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:32'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->phone = $request->phone;
        $user->save(); 

        return response()->json(['status' => 'Success']);
    }

    public function saveUserdata(Request $request)
    {
        $request->validate([
            'email' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255'
        ]);

        $user = JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user->email = $request->email;
        $user->name = $request->name;
        $user->save(); 

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