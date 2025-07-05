<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\TravelOrder\TravelOrderController;
use App\Http\Controllers\User\CreateUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['message' => 'Tô funcionando, sô']));

Route::post('/auth/register', CreateUserController::class);
Route::post('/auth/login', LoginController::class);
Route::middleware('jwt')->group(function () {
    Route::post('/auth/logout', LogoutController::class);
    Route::prefix('/travel-orders')->group(function () {
        Route::post('/', [TravelOrderController::class, 'store']);
        Route::get('/', [TravelOrderController::class, 'index']);
        Route::get('/{travel_order}', [TravelOrderController::class, 'show']);
        Route::patch('/{travel_order}/accept', [TravelOrderController::class, 'accept']);
        Route::patch('/{travel_order}/cancel', [TravelOrderController::class, 'cancel']);
    });
});
