<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function create(Request $request)
    {
        $user = Auth::user();
        $request->validate(['title' => 'required', 'description' => 'required']);
        $course = Course::create(['user_id' => $user->id, 'title' => $request->title, 'description' => $request->description, 'step' => 1]);
        return response()->json(['message' => 'course successfully created', 'data' => $course]);
    }

}
