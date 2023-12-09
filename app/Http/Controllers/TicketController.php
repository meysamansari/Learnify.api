<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Course;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{



    public function index()
    {
        $comments = Ticket::latest('updated_at')->paginate(10);
        return response()->json(['data' => $comments]);
    }



    public function store(StoreTicketRequest $request, $course_id)
    {
        $user = Auth::user();
        Course::findOrFail($course_id);
        $ticket = Ticket::create([
            'message' => $request->message,
            'reply' => null,
            'course_id' => $course_id,
            'user_id' => $user->id,
        ]);
        return response()->json(['message' => 'Ticket successfully created', 'data' => [$ticket]]);
    }
}
