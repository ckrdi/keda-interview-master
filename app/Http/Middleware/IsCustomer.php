<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsCustomer
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
        if (auth()->user()->userType->name !== 'Customer') {
            return response()->json([
                'message' => 'You are not a customer, you will be redirected to the staff login page'
            ], 302);
        }

        return $next($request);
    }
}
