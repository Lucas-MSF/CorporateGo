<?php

use App\Http\Controllers\User\CreateUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => response()->json(['message' => 'Tô funcionando, sô']));

Route::post('/register', CreateUserController::class);
Route::middleware('jwt')->group(function () {

});
