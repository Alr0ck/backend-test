<?php

namespace App\Repositories\Auth;

use App\Repositories\Auth\IAuthRepository;
use App\User;
use App\Constants\PaginatorConst;
use Illuminate\Pagination\Paginator;
use Auth;
use DB;
use App\Services\Keyword;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;

class AuthRepository implements IAuthRepository
{
    public function doLogin($request, $attr){
        if(isset($attr->email)) {
            $user = User::where('email',$attr->email)->first();

            if($user == null){
                throw ValidationException::withMessages([
                    'user' => 'Email / Password Tidak Valid'
                ]);
            }

            if($user->status != 'confirmed'){
                throw ValidationException::withMessages([
                    'user' => 'Pengguna Tidak Dapat Ditemukan'
                ]);
            }

            if(!Hash::check($attr->password, $user->password)){
                throw ValidationException::withMessages([
                    'user' => 'Email / Password Tidak Valid'
                ]);
            }

            $token = app('auth')->attempt(['email' => $user->email, 'password' => $attr->password]);
            $expired = \Carbon\Carbon::now()->addMinute(60);

            return array('user' => $user, 'token' => $token, 'expired' => $expired);
        }

        throw ValidationException::withMessages([
            'user' => 'Email Tidak Valid'
        ]);
    }
}

