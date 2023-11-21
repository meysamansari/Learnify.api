<?php

namespace App\Listeners;

use App\Events\SmsVerificationCode;
use GuzzleHttp\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        $url = "http://ippanel.com:8080/";
        $data = array(
            "apikey" => "lKV7mXyFy99HZ16vlZ5_X0UgQtSkSY6vE_6sd7YTtYQ=",
            "pid" => "giangz952u69pm7",
            "fnum" => "09810004223",
            "tnum" => "$event->phone_number",
            "p1" => "verification-code",
            "v1" => "$event->verification_code",
        );
        $client = new Client();
        $client->request('GET', $url, [
            'query' => $data
        ]);
    }
}
