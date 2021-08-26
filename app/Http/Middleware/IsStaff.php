<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->userType->name !== 'Staff') {
            return response()->json([
                'message' => 'You are not a staff, you will be redirected to the customer login page'
            ], 302);
        }

        return $next($request);
    }
}
