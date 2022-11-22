<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
  
    public function index()
    {
        $user = User::all();
        return Response($user);
    }

    protected function guard() {
        return Auth::guard();
    }
}
