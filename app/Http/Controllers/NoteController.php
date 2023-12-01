<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }


    public function UpdateOrCreate(Request $request, $course_id)
    {
        $user = Auth::user();
        $note = Note::where('user_id', $user->id)
            ->where('course_id', $course_id)
            ->first();
        if ($note) {
            $note->description = $request->description;
            $note->save();
            return response()->json(['message' => 'successfully updated note', 'data' => [$note]]);
        } else {
            $note = Note::create([
                'user_id' => $user->id,
                'course_id' => $course_id,
                'description' => $request->description
            ]);
        }
        return response()->json(['message' => 'successfully created note', 'data' => [$note]]);
    }


    public function show($id)
    {
        try {
            $note = Note::findOrFail($id);
            return response()->json(['data' => $note]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'note not found'], 404);
        }
    }


    public function destroy($id)
    {
        try {
            $note = Note::findOrFail($id);
            $note->delete();
            return response()->json(['message' => 'successfully deleted note']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'note not found'], 404);
        }
    }
}
