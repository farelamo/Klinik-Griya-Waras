<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DrugController;

Route::resource('drug', DrugController::class)->except(['create', 'edit']);

