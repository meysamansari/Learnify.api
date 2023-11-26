<?php

namespace App\Listeners;

use App\Events\SmsVerificationCode;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use IPPanel\Client;

class SendSmsVerification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SmsVerificationCode $event): void
    {
        $client = new Client("M4dKQmfmTaTS6b3OO3MS3UEkP7ppH7ygz74enbZ6P-g=");
        $client->sendPattern("cffe8qv29mvmcsl","+9890000145","$event->phone_number",[
            "verification-code" => "$event->verification_code",
        ]);
    }
}
