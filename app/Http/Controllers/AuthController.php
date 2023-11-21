<?php

namespace App\Http\Controllers;

use App\Events\SmsVerificationCode;
use App\Models\Verification;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        $phone_number = $request->input('phone_number');
        $verification_code = rand(10000, 99999);
        Verification::UpdateOrCreate(['phone_number' => $phone_number], ['verification_code' => $verification_code, 'verification_valid_until' => now()->addMinutes(5),]);
        event(new SmsVerificationCode($phone_number, $verification_code));
        return response()->json(['message' => 'Verification code send successfully']);
    }

}
