<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth.api')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::resource('drug', DrugController::class)->except(['create', 'edit']);
    Route::resource('user', UserController::class)->except(['create', 'edit']);
});
