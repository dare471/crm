<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
/* use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response; */
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' =>['login', 'register']]);
    }

    public function login(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:3',
        ]);

        if($validator->fails()) 
        {
            return response()->json($validator->errors(), 400);
        }
        $token_validity = (24*60); //24ч*60м

        $this->guard()->factory()->setTTL($token_validity);

        if(!$token = $this->guard()->attempt($validator->validated())) 
        {
            return response()->json(['status' => false ,'error' => 'Не авторизован!'], 401);
        }

        return $this->respondWithToken($token); 
    }


    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([$validator->errors()], 422);
        }

        $user = User::create(
            array_merge($validator->validated(), ['password' => bcrypt($request->password)])
        );
        $token_validity = (24*60); //24ч*60м

        $this->guard()->factory()->setTTL($token_validity);
        if(!$token = $this->guard()->attempt($validator->validated())) 
        {
            return response()->json(['status' => false ,'error' => 'Не авторизован!'], 401);
        }
        return response()->json(['message' => 'Пользователь успешно создан!', 'user' => $user, 'jwt_token' => $this->shortifnT($token) ]);
    }


    public function logout() 
    {
        $this->guard()->logout();
        return response()->json(['message' => 'Пользователь успешно вышел из аккаунта!' ]);
    }


    public function profile() 
    {
        return response()->json($this->guard()->user());
    }


    public function refresh() 
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function shortifnT($token) 
    {
        return response()->json([
            'token_user'=>['users_id'=>$this->guard()->user()->id,
            'email'=>$this->guard()->user()->email,
            'name'=>$this->guard()->user()->name,
            'access_availability'=>json_decode($this->guard()->user()->access_availability),
            'active'=>$this->guard()->user()->active,
            'subscribesRegion'=>$this->guard()->user()->region_belongs,
            'unFollowClients'=>$this->guard()->user()->unfollowClient,
            'work_position'=>$this->guard()->user()->work_position,
            'status'=> true,
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => ($this->guard()->factory()->getTTL() * 60),
            ]]);
    }


    protected function respondWithToken($token) 
    {
        $arr=array();
        $query = DB::table("CRM_CLIENT_TO_VISIT")
        ->select("CLIENT_ID")
        ->where("USER_ID", $this->guard()->user()->id)
        ->get();
        foreach($query as $q){
            array_push($arr, (int)$q->CLIENT_ID);
        }

        return response()->json([
            'user'=>['id'=>$this->guard()->user()->id,
            'email'=>$this->guard()->user()->email,
            'name'=>$this->guard()->user()->name,
            'access_availability'=>json_decode($this->guard()->user()->access_availability),
            'workPosition'=>$this->guard()->user()->work_position,
            'active'=>(int)$this->guard()->user()->activated,
            'unFollowClients'=>json_decode($this->guard()->user()->unfollowClient)->clientId,
            'favoriteClients' => $arr,
            'subscribesRegion'=>json_decode($this->guard()->user()->region_belongs)->region
        ],
            'status'=> true,
            'token' => $token,
            'token_type' => 'bearer',
            'token_validity' => ($this->guard()->factory()->getTTL() * 60),
        ]);
    }


    protected function guard() 
    {
        return Auth::guard();
    }
}
