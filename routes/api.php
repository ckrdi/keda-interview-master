<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([ 'prefix' => 'customer' ], function() {
    Route::post('login', [CustomerController::class, 'login'])->name('customerLogin');
});

Route::group([ 'prefix' => 'staff' ], function() {
    Route::post('login', [StaffController::class, 'login']);
});

Route::group([
    'prefix' => 'customer',
    'middleware' => ['auth:api', 'customer']
], function() {
    Route::post('logout', [CustomerController::class, 'logout']);

    Route::get('messages', [CustomerController::class, 'messages']);
    Route::post('messages', [MessageController::class, 'store']);
});

Route::group([
    'prefix' => 'staff',
    'middleware' => ['auth:api', 'staff']
], function() {
    Route::post('logout', [StaffController::class, 'logout']);

    Route::get('messages', [StaffController::class, 'messages']);
    Route::post('messages', [MessageController::class, 'store']);

    Route::get('customers', [StaffController::class, 'customers']);
    Route::delete('customers/{id}', [StaffController::class, 'deleteCustomer']);
    Route::post('customers/{id}', [StaffController::class, 'restoreCustomer']);
});