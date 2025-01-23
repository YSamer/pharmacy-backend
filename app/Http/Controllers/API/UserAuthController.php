<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Request;
use App\Services\UserAuthService;
use Illuminate\Support\Facades\App;

class UserAuthController extends Controller
{
    public $userAuthService;
    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     **/
    public function reqister(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->registerUser($data);

        return response($user);
    }
}
