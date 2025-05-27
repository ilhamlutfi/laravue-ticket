<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Container\Attributes\Auth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login')
    ->middleware([
        'guest', // Ensure the user is not authenticated
        'throttle:6,1' // Throttle to prevent abuse
    ]);

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware([
        'auth:sanctum', // Ensure the user is authenticated
    ]);
