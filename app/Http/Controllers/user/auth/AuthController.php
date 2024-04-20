<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\users\Users;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'sometimes|required_without_all:phone,bin|email',
            'phone' => 'sometimes|required_without_all:email,bin',
            'bin' => 'sometimes|required_without_all:email,phone',
            'password' => 'required|string|min:6',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        if (!$token = $this->attemptLogin($request)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    
        return $this->respondWithToken($token);
    }
    
    

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'bin' => 'required|string|unique:users',  // Убедитесь, что это правильная таблица
            'phone' => 'required|string|max:16',
            'email' => 'required|email|unique:users|min:6',  // Проверка на уникальность email
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = Users::create([
            'name' => $request->name,
            'bin' => $request->bin,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token, 'message' => 'User successfully registered']);
    }


    protected function attemptLogin(Request $request)
    {
        $credentials = $request->only('email', 'phone', 'bin', 'password');
    
        foreach (['email', 'phone', 'bin'] as $field) {
            if (!empty($credentials[$field])) {
                // Указываем использование новой гвардии client_api
                if ($token = auth('api')->attempt([$field => $credentials[$field], 'password' => $credentials['password']])) {
                    return $token;
                }
            }
        }
    
        return false;
    }
    

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }

    public function userProfile(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            return response()->json(['user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'user_not_found'], 404);
        }
    }
}
