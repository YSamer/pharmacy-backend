<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserVerifyPhoneRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Services\UserAuthService;

class UserAuthController extends Controller
{
    public UserAuthService $userAuthService;
    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function reqister(UserRegisterRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->registerUser($data);
        if (!$user) {
            return $this->errorResponse('Failed to register user.', 500);
        }

        $this->userAuthService->sendOTP($user);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'User registered successfully.',
        );
    }

    public function login(UserLoginRequest $request)
    {

        $data = $request->validated();

        $user = $this->userAuthService->loginUser($data);
        if (!$user) {
            return $this->errorResponse(
                'Invalid phone number.',
                401
            );
        }

        $this->userAuthService->sendOTP($user);

        return $this->successResponse(
            ['user' => new UserResource($user)],
            'User Login successfully.',
        );
    }

    public function verifyPhone(UserVerifyPhoneRequest $request)
    {
        $data = $request->validated();

        $user = $this->userAuthService->verifyPhone($data);
        if (!$user) {
            return $this->errorResponse(
                'Invalid OTP.',
                401
            );
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse(
            ['user' => new UserResource($user), 'token' => $token],
            'User Login successfully.',
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(
            null,
            'User logged out successfully.',
        );
    }

    public function user(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return $this->successResponse(
                null,
                'Authentication failed. User not found.',
            );
        }
        return $this->successResponse(
            ['user' => new UserResource($user)],
            'User authenticated successfully.',
        );
    }
}
