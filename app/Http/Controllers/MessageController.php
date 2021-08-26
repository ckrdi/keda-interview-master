<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'message' => 'required',
            'recipient' => 'required'
        ]);

        if (auth()->user()->id == $request->recipient) {
            return response()->json([
                'message' => 'You cannot send message to yourself'
            ], 405);
        }

        $response = Message::create([
            'sender_id' => auth()->user()->id,
            'sent_to_id' => $request->recipient,
            'subject' => $request->subject,
            'message' => $request->message
        ]);

        return response()->json($response, 201);
    }
}
