<?php

namespace App\Http\Controllers;
use App\Models\LdapUser;
use Adldap\Laravel\Facades\Adldap;
use Illuminate\Http\Request;

class ClientAuthController extends Controller
{
    /**
    
     */
    public function index(Request $request)
    {
        $username = 'd.onglassyn';

        $users = Adldap::search()->where('samaccountname', '=', $username)->get();
    
        if ($users->count() > 0) {
            // Пользователь найден, обрабатываем данные
            $user = $users->first();
            return $user->getAttributes();
        } else {
            // Пользователь не найден
            return 'Пользователь не найден';
        }
    }
        


    /**
    
     */
    public function create()
    {
        //
    }

    /**
     
     */
    public function store(Request $request)
    {
        //
    }

    /**
     */
    public function show($id)
    {
        //
    }

    /**
   
     */
    public function edit($id)
    {
        //
    }

    /**
     
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
    
     */
    public function destroy($id)
    {
        //
    }
}
