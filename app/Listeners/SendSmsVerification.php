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
        $client = new Client("lKV7mXyFy99HZ16vlZ5_X0UgQtSkSY6vE_6sd7YTtYQ=");
        $client->sendPattern("giangz952u69pm7","+9890000145","$event->phone_number",[
            "verification-code" => "$event->verification_code",
        ]);
    }
}
