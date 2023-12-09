<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{



    public function index()
    {
        $comments = Comment::latest('updated_at')->paginate(10);
        return response()->json(['data' => $comments]);
    }



    public function store(StoreCommentRequest $request, $course_id)
    {
        $user = Auth::user();
        Course::findOrFail($course_id);
        $existingComment = $user->comments()->where('course_id', $course_id)->first();
        $rate = $existingComment ? null : $request->rate;
        $comment = Comment::create([
            'message' => $request->message,
            'reply' => null,
            'course_id' => $course_id,
            'user_id' => $user->id,
            'rate' => $rate
        ]);
        return response()->json(['message' => 'Comment successfully created', 'data' => [$comment]]);
    }
}
