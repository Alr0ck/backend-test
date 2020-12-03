<?php

namespace App\Repositories\Auth;

use App\Repositories\Auth\IAuthRepository;
use App\User;
use App\Constants\PaginatorConst;
use App\Mail\DoRegisterMail;
use App\DTO\DoRegisterDto;
use Illuminate\Pagination\Paginator;
use Auth;
use DB;
use App\Services\Keyword;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Log;

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

    public function doRegister($request){
        $transac = DB::transaction(function() use ($request){
            $user = new User;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->status = 'new';
            $user->save();
            
            try {
                $emailData = new DoRegisterDto([
                    'validateToken' => base64_encode($user->id.'::'.$request->email),
                ]); 
                Mail::to($user->email)->queue(new DoRegisterMail($emailData));
            } catch (\Throwable $th) {
                Log::error('[Register Email] : ' . $th);
            }

            return compact('user');
        });

        return $transac['user'];
    }

    public function validateRegister($request){
        $token = isset($request->token) ? $request->token : null;
        if($token != null){
            $decode = base64_decode($token);
            $data = explode('::',$decode);
            $userId = isset($data[0]) ? $data[0] : null;
            $email = isset($data[1]) ? $data[1] : null;
            
            if($userId != null && $email != null){
                $user = User::where('id',$userId)->where('email',$email)->where('status','new')->first();
                if($user != null){
                    $user->status = 'confirmed';
                    $user->save();
                    return $user;
                }
            }
        }
        return null;
    }
}

