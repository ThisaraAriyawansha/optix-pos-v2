<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;

// Login Page
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'postLogin'])->name('login.post');

//Home Page
Route::get('/home', [HomeController::class, 'home'])->name('home')->middleware('auth');

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

//Branches Page
Route::get('/branches',[BranchController::class, 'index'])->name('branches');
Route::get('/branches/create', [BranchController::class, 'create'])->name('branches.create');
Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
Route::get('/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
Route::patch('/branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])->name('branches.toggleStatus');


// Users Page
Route::get('/users', [UserController::class, 'index'])->name('users');
Route::get('/users/manage', [UserController::class, 'manage'])->name('users.manage');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

// User Roles
Route::get('/users/roles', [UserRoleController::class, 'manage'])->name('roles.manage');
Route::get('/users/roles/create', [UserRoleController::class, 'create'])->name('roles.create');
Route::post('/users/roles', [UserRoleController::class, 'store'])->name('roles.store');