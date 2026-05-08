<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;


// Login Page
Route::get('/', [LoginController::class, 'login'])->name('login');