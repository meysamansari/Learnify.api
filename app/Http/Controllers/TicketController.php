<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReplyTicketRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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


    public function show($ticket_id)
    {
        try {
            $ticket = Ticket::findOrFail($ticket_id);
            return response()->json(['message' => 'Ticket retrieved successfully', 'data' => $ticket]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            if ($ticket->reply === null) {
                $ticket->update([
                    'message' => $request->message
                ]);
                return response()->json(['message' => 'Ticket updated successfully']);
            } else {
                return response()->json(['error' => 'Cannot update the ticket'], 400);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }


    public function reply(ReplyTicketRequest $request, $ticket_id)
    {
        try {
            $reply = Ticket::findOrFail($ticket_id);
            $reply->update([
                'reply' => $request->reply,
            ]);
            return response()->json(['message' => 'Reply added successfully', 'data' => $reply]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Reply not found'], 404);
        }
    }


    public function destroy($id)
    {
        try {
            $comment = Ticket::findOrFail($id);
            $comment->delete();
            return response()->json(['message' => 'Ticket deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
    }
}
