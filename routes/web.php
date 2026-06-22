<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;

// Login Page
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'postLogin'])->name('login.post');

//Home Page
Route::get('/home', [HomeController::class, 'home'])->name('home')->middleware('auth');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');