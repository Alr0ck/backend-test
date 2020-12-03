<?php

namespace App\Repositories\Auth;

interface IAuthRepository
{
    public function doLogin($request, $attr);
}
