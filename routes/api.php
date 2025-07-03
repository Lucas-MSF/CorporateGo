<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\User\CreateUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['message' => 'Tô funcionando, sô']));

Route::post('/register', CreateUserController::class);
Route::post('/auth/login', LoginController::class);
Route::middleware('jwt')->group(function () {
    Route::post('/auth/logout', LogoutController::class);
});
