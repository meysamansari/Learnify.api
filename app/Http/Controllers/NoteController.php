<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{

    public function store(Request $request)
    {
        $user = Auth::user();
        return response()->json(Note::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'description' => $request->description

        ]));
    }
    public function show($id)
    {
        $note = Note::find($id);
        return response()->json(
            $note
        );
    }
}

