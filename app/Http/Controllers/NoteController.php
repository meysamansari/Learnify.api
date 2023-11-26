<?php

namespace App\Http\Controllers;

use App\Models\Note;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{


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
        $note = Note::find($id);
        return response()->json(['data' => $note]);
    }


    public function delete($id)
    {
        if (Note::where('id', $id)->exists()) {
            Note::destroy($id);
            return response()->json(['message' => 'successfully deleted note', 'data' => [$id]]);
        } else {
            return response()->json(['message' => 'note not found']);
        }
    }

}
