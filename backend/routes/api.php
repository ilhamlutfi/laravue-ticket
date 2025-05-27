<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Container\Attributes\Auth;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\DashboardController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware([
        'guest', // Ensure the user is not authenticated
        'throttle:6,1' // Throttle to prevent abuse
    ]);
Route::post('/register', [AuthController::class, 'register'])
    ->middleware([
        'guest',
        'throttle:6,1'
    ]);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'getUser']);

    Route::apiResource('/tickets', TicketController::class);
    Route::post('/tickets-reply/{code}', [TicketController::class, 'storeReply']);

    Route::get('/dashboard', [DashboardController::class, 'getStatistics']);
});

