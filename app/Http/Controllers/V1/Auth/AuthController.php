<?php

namespace App\Http\Controllers\V1\Auth;

use App\Http\Controllers\V1\Controller;
use Illuminate\Http\Request;
use App\Repositories\Auth\IAuthRepository;
use App\Transformers\LoginTransformer;
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

}
