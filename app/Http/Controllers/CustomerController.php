<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate user
            $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            // Check email
            $user = User::where('email', $request->email)->first();

            // Check password
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'The provided credentials do not match our records.'
                ], 401);
            }

            // Check if the user is a customer
            if ($user->userType->name !== 'Customer') {
                return response()->json([
                    'message' => 'You are not a customer, you will be redirected to the staff login page'
                ], 302);
            }

            $accessToken = $user->createToken('authToken')->accessToken;

            $response = [
                'user' => $user,
                'access_token' => $accessToken
            ];
            
            return response()->json($response, 201);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            
            return response()->json([
                'message' => 'Successfully logged out'
            ], 200);
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function messages(Message $message)
    {
        return response()->json([
            'sent_messages' => $message->where('sender_id', auth()->user()->id)->latest('id')->get(),
            'received_messages' => $message->where('sent_to_id', auth()->user()->id)->latest('id')->get()
        ], 200);
    }
}
