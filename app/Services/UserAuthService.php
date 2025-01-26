<?php

namespace App\Services;

use App\Events\SendOtpEvent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAuthService
{

    public function registerUser(array $data): ?User
    {
        $data['image'] = storeFile($data['image'] ?? null, 'images');

        $user = User::create($data);

        return $user;
    }

    public function loginUser(array $data): ?User
    {
        $user = User::where('phone', $data['phone'])->first();

        if ($user) {
            return $user;
        }

        return null;
    }

    public function verifyPhone(array $data): ?User
    {
        $user = User::where('phone', $data['phone'])->first();

        if ($user && $user->otp === $data['otp']) {
            $user->otp = null;
            $user->save();

            return $user;
        }

        return null;
    }

    public function sendOTP(User $user): ?string
    {
        // $otp = mt_rand(1111, 9999);
        $otp = '1234';
        $user->otp = $otp;
        $user->save();

        event(new SendOtpEvent($user, $otp));

        return 'OTP sent successfully.';
    }

    public function sendSMS($user, $otp): bool
    {
        // Send SMS using SMS Gateway API or any other service
        Log::info('Send SMS using SMS Gateway API or any other service');

        return true;
    }
}