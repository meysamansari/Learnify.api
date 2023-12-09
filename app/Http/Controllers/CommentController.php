<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyCommentRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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



    public function show($comment_id)
    {
        try {
            $comment = Comment::findOrFail($comment_id);
            return response()->json(['message' => 'Comment retrieved successfully', 'data' => $comment]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
    }



    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);
            if ($comment->reply === null && $comment->rate === null) {
                $comment->update([
                    'message' => $request->message,
                    'rate' => $request->rate,
                ]);
                return response()->json(['message' => 'Comment updated successfully']);
            } else {
                return response()->json(['error' => 'Cannot update the comment'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
    }



    public function reply(ReplyCommentRequest $request, $comment_id)
    {
        try {
            $reply = Comment::findOrFail($comment_id);
            $reply->update([
                'reply' => $request->reply,
            ]);
            return response()->json(['message' => 'Reply added successfully', 'data' => $reply]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
    }



    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $comment->delete();
            return response()->json(['message' => 'Comment deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Comment not found'], 404);
        }
    }
}
