<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserVerifyPhoneRequest;
use Illuminate\Http\Request;
use App\Services\UserAuthService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserAuthController extends Controller
{
    public UserAuthService $userAuthService;
    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    public function reqister(UserRegisterRequest $request)
    {
        try {
            $data = $request->validated();

            $user = $this->userAuthService->registerUser($data);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to register user.',
                ], 500);
            }

            $this->userAuthService->sendOTP($user);

            return response()->json([
                'data' => [
                    'user' => $user,
                ],
                'message' => 'User registered successfully.',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('User registration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function login(UserLoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = $this->userAuthService->loginUser($data);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number.',
                ], 401);
            }

            $this->userAuthService->sendOTP($user);

            return response()->json([
                'data' => [
                    'user' => $user,
                ],
                'message' => 'User Login successfully.',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('User login failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyPhone(UserVerifyPhoneRequest $request)
    {
        try {
            $data = $request->validated();

            $user = $this->userAuthService->verifyPhone($data);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ], 401);
            }
            $token = $user->createToken('auth_token')->plainTextToken;


            return response()->json([
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'message' => 'User Logged In successfully.',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('User Logged In failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message' => 'User logged out successfully.',
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('User logged out failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function user(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'user' => $user,
                    ],
                    'message' => 'User authenticated successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Authentication failed. User not found.',
            ], 401);
        } catch (\Exception $e) {
            Log::error('User Auto logged In failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
