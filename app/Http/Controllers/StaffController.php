<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
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

            // Check if the user is a staff
            if ($user->userType->name !== 'Staff') {
                return response()->json([
                    'message' => 'You are not a staff, you will be redirected to the customer login page'
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
            'all_messages' => $message->where('sender_id', '!=', auth()->user()->id)
                                    ->where('sent_to_id', '!=', auth()->user()->id)
                                    ->latest('id')
                                    ->get(),
            'sent_messages' => $message->where('sender_id', auth()->user()->id)->latest('id')->get(),
            'received_messages' => $message->where('sent_to_id', auth()->user()->id)->latest('id')->get()
        ], 200);
    }

    public function customers(User $user)
    {
        return response()->json([
            'customers' => $user->withTrashed()->where('user_type_id', 1)->get()
        ], 200);
    }

    public function deleteCustomer(User $user, $id)
    {
        $customer = $user->where('user_type_id', 1)->where('id', $id)->first();

        $customer->delete();

        return response()->json([
            'message' => 'Successfully deleted'
        ], 200);
    }

    public function restoreCustomer(User $user, $id)
    {
        $customer = $user->withTrashed()->where('user_type_id', 1)->where('id', $id)->first();

        $customer->restore();

        return response()->json([
            'customer' => $customer,
            'message' => 'Successfully restored'
        ], 200);
    }
}
