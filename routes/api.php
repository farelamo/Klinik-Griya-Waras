<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\UserController;

Route::resource('drug', DrugController::class)->except(['create', 'edit']);
Route::resource('user', UserController::class)->except(['create', 'edit']);