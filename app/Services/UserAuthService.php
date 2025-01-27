<?php

namespace App\Services;

use App\Events\SendOtpEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserAuthService
{

    public function registerUser(array $data): ?User
    {
        try {
            $data['image'] = storeFile($data['image'] ?? null, 'images');

            $user = User::create($data);
        } catch (\Exception $e) {
            Log::error('Failed to register user', ['error' => $e->getMessage()]);
            return null;
        }

        return $user;
    }

    public function loginUser(array $data): ?User
    {
        $user = User::where('phone', $data['phone'])->first();

        if (!$user) {
            Log::warning("Login attempt failed for phone: {$data['phone']}");
            return null;
        }

        return $user;
    }

    public function verifyPhone(array $data): ?User
    {
        $user = User::where('phone', $data['phone'])
            ->whereNotNull('otp')
            ->where('otp_expires_at', '>=', now())
            ->first();

        if ($user && Hash::check($data['otp'], $user->otp)) {
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            return $user;
        }

        return null;
    }

    public function updateProfile(array $data): ?User
    {
        $user = Auth::user();
        if (!$user || !$user instanceof User) {
            Log::warning('Update profile attempt without authenticated user.');
            return null;
        }

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['email'])) {
            $user->email = $data['email'];
        }

        if (isset($data['image'])) {
            if ($user->image) {
                deleteFile($user->image);
            }

            $user->image = storeFile($data['image'] ?? null, 'images');
        }

        $user->save();

        return $user;
    }

    public function sendOTP(User $user): ?string
    {
        // $otp = random_int(1111, 9999);
        $otp = '1234';
        $user->otp = Hash::make($otp);
        $user->otp_expires_at = now()->addMinutes(1);
        $user->save();

        event(new SendOtpEvent($user, $otp));

        return 'OTP sent successfully.';
    }

    public function sendSMS($user, $otp): bool
    {
        // Send SMS using SMS Gateway API or any other service
        Log::info("Sending SMS to {$user->phone}: Your OTP is $otp");

        return true;
    }
}