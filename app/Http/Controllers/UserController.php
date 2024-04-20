<?php

namespace App\Http\Controllers;

use App\Models\users\Users;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
