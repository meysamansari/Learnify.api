<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        $phoneNumber = $user->phone_number;
        if($user->hasRole('mentor')&&$request->description){
            $resume = $user->resume;
            if (!$resume) {
                $resume = new Resume();
                $resume->user_id = $user->id;
            }
            $resume->description = $request->description;
            $resume->save();
        }
        if($user->hasRole('student')&&$request->favorites){
            $user->favorites()->detach();
            $favorites = $request->favorites;
            foreach ($favorites as $favorite) {
                $favoriteModel = Favorite::find($favorite['id']);
                if ($favoriteModel) {
                    $favoriteModel->users()->attach($user->id);
                }
        }
            }
        $request = $request->except('_token', 'phone_number');
        $update_user = User::where('phone_number', $phoneNumber)->get();
        if ($update_user->isNotEmpty()) {
            foreach ($update_user as $user) {
                $user->update($request);
                if($user->hasRole('student')){
                    $data = $user->load('favorites');
                }
                if($user->hasRole('mentor')){
                    $data = $user->load('resume');
                }
            }
            return response()->json(['message' => 'user information successfully updated', 'user' => $data]);
        }
        return response()->json(['message' => 'user not found'], 404);
    }
    public function show()
    {
        $user = Auth::user();

        if ($user->hasRole('mentor')) {
            $data = $user->load('resume');
            return response()->json(['message' => 'Mentor retrieved successfully', 'data' => $data]);
        } else if ($user->hasRole('student')) {
            $data = $user->load('favorites');
            return response()->json(['message' => 'Student retrieved successfully', 'data' => $data]);
        }
    }
}
