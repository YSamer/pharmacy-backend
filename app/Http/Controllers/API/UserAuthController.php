<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserDeleteAccountRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateProfileRequest;
use App\Http\Requests\UserVerifyPhoneRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\UserAuthService;

class UserAuthController extends Controller
{
    private UserAuthService $userAuthService;
    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->registerUser($data);
        if (!$user) {
            return $this->errorResponse(__('messages.register_fail'), 500);
        }

        $this->userAuthService->sendOTP($user);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            __('messages.register_success'),
        );
    }

    public function login(UserLoginRequest $request)
    {

        $data = $request->validated();

        $user = $this->userAuthService->loginUser($data);
        if (!$user) {
            return $this->errorResponse(__('messages.login_fail'), 401);
        }

        $this->userAuthService->sendOTP($user);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            __('messages.login_success'),
        );
    }

    public function verifyPhone(UserVerifyPhoneRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->verifyPhone($data);
        if (!$user) {
            return $this->errorResponse(__('messages.verify_fail'), 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user), 'token' => $token],
            __('messages.verify_success'),
        );
    }

    public function logout(Request $request)
    {
        $this->userAuthService->logout();

        return $this->successResponse(
            null,
            __('messages.logout_success'),
        );
    }

    public function user(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->successResponse(
                null,
                __('messages.auth_failed'),
            );
        }
        return $this->successResponse(
            ['user' => new UserResource($user)],
            __('messages.auth_success'),
        );
    }

    public function updateProfile(UserUpdateProfileRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->updateProfile($data);
        if (!$user) {
            return $this->errorResponse(__('messages.update_profile_fail'), 401);
        }

        return $this->successResponse(
            ['user' => new UserResource($user)],
            __('messages.update_profile_success'),
        );
    }

    public function deleteAccount(UserDeleteAccountRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->deleteAccount($data);
        if (!$user) {
            return $this->errorResponse(__('messages.auth_failed'), 401);
        }

        return $this->successResponse(
            null,
            __('messages.account_delete_success'),
        );
    }
}
