<?php

namespace App\Http\Controllers;

use App\Utils\HasShop;
use Illuminate\Auth\AuthManager;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Tymon\JWTAuth\JWTGuard;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, HasShop;

    /* @var JWTGuard $auth */
    protected $auth;

    /**
     * ShopController constructor.
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|\App\User|null
     */
    public function getUser()
    {
        return $this->auth->user();
    }
}
