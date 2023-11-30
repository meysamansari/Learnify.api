<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $phoneNumber = $user->phone_number;

        $data = $request->except('_token', 'phone_number');
        $update_user = User::where('phone_number', $phoneNumber)->get();
        if ($update_user->isNotEmpty()) {
            foreach ($update_user as $user) {
                $user->update($data);
            }
            return response()->json(['message' => 'user information successfully updated', 'data' => $update_user]);
        }
        return response()->json(['message' => 'user not found'], 404);
    }
}
