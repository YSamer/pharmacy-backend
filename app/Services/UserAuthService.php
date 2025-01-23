<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{

    public function registerUser($data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['otp'] = '1234';

        $data['image'] = storeFile($data['image'], 'images');

        $user = User::create($data);

        return $user;
    }



}