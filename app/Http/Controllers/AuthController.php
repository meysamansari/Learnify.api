<?php

namespace App\Http\Controllers;

use App\Events\SmsVerificationCode;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\VerificationRequest;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['sendVerificationCode','login']]);
    }
    public function sendVerificationCode(VerificationRequest $request)
    {
        $phone_number = $request->input('phone_number');
        $verification_code = rand(10000, 99999);
        Verification::UpdateOrCreate(['phone_number' => $phone_number], ['verification_code' => $verification_code, 'verification_valid_until' => now()->addMinutes(5),]);
        event(new SmsVerificationCode($phone_number, $verification_code));
        return response()->json(['message' => 'Verification code send successfully']);
    }
    function login(LoginRequest $request, $type)
    {
        $phone_number = $request->input('phone_number');
        $verification_code = $request->input('verification_code');
        $verification = Verification::query()->where('phone_number', $phone_number)->first();
        if (!$verification) {
            return response()->json(['status' => 401, 'message' => 'user not found']);
        }
        if ($verification->verification_code == $verification_code) {
            if (now() > $verification->verification_valid_until) {
                return response()->json(['status' => 401, 'message' => 'verification code is expired']);
            }
            if (User::where('phone_number', $phone_number)->exists()) {
                $user = User::where('phone_number', $phone_number)->first();
                $userRole = User::where('phone_number', $phone_number)->whereHas('roles', function ($query) use ($type) {
                    $query->where('name', $type);
                })->first();
                $admin = 'admin';
                $adminUser = User::where('phone_number', $phone_number)->whereHas('roles', function ($query) use ($admin) {
                    $query->where('name', $admin);
                })->first();
                if ($adminUser) {
                    $userRole = $adminUser;
                    $type = 'admin';
                }
                else if (!$userRole) {
                    $userRole = User::Create([
                        'name' => $user->name,
                        'family' => $user->family,
                        'email' => $user->middle_name,
                        'phone_number' => $phone_number,
                        'age' => $user->age,
                        'gender' => $user->gender,
                        'university' => $user->university,
                        'field_of_study' => $user->field_of_study,
                        'educational_stage' => $user->educational_stage,
                        'country' => $user->country,
                        'state' => $user->state,
                    ]);
                    $userRole->assignRole($type);
                }
            } else {
                $userRole = User::Create([
                    'phone_number' => $phone_number]);
                $userRole->assignRole($type);
            }
            $token = $userRole->createToken('api_token')->plainTextToken;
            $verification->verification_code = null;
            $verification->verification_valid_until = null;
            $verification->save();

            return response()->json(['status' => 200, 'message' => 'user logged in successfully', 'data' => [$userRole, $type],
                'token' => $token]);
        }
        return response()->json(['status' => 401, 'message' => 'verification code is wrong']);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();
        return response()->json([
            'message' => 'User logged out successfully',
        ]);
    }
}
