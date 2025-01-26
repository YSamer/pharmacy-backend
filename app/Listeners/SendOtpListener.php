<?php

namespace App\Listeners;

use App\Events\SendOtpEvent;
use App\Models\User;
use App\Services\UserAuthService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendOtpListener
{
    public UserAuthService $userAuthService;
    public function __construct(UserAuthService $userAuthService)
    {
        $this->userAuthService = $userAuthService;
    }

    /**
     * Handle the event.
     */
    public function handle(SendOtpEvent $event): void
    {
        $this->userAuthService->sendSms($event->user, $event->otp);
    }
}
