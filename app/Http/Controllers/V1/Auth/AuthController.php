<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\V1\Controller;
use Illuminate\Http\Request;
use App\Repositories\Auth\IAuthRepository;
use App\Transformers\LoginTransformer;
use App\Transformers\RegisterTransformer;
use Illuminate\Auth\Access\AuthorizationException;

class AuthController extends Controller
{
    protected $authRepo;

    public function __construct(
        IAuthRepository $authRepo
    )
    {
        $this->authRepo = $authRepo;
    }
    
    public function doLogin(Request $request)
    {
        $attr = $this->resolveRequest($request);
        $login =  $this->authRepo->doLogin($request,$attr);
        return $this->singleResponse(
            $request,
            $login, 'logins',
            new LoginTransformer()
        );
    }

    public function doRegister(Request $request){
        $attr = $this->resolveRequest($request);
        $user =  $this->authRepo->doRegister($attr);
        return $this->singleResponse(
            $request,
            $user, 'registers',
            new RegisterTransformer()
        );
    }

    public function validateRegister(Request $request){
        $user =  $this->authRepo->validateRegister($request);

        if($user == null){
            return $this->emptyResponse('Validasi User Baru Gagal, Data Tidak Valid');
        }

        return $this->singleResponse(
            $request,
            $user, 'registers-validate',
            new RegisterTransformer()
        );
    }

}
