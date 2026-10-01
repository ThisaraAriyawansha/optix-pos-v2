<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;



// Login Page
Route::get('/', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'postLogin'])->name('login.post');

//Home Page
Route::get('/home', [HomeController::class, 'home'])->name('home')->middleware('auth');

// Language switch
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, \App\Http\Middleware\SetLocale::SUPPORTED, true)) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

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
Route::get('/users/roles/{role}/edit', [UserRoleController::class, 'edit'])->name('roles.edit');
Route::put('/users/roles/{role}', [UserRoleController::class, 'update'])->name('roles.update');


//Suppliers Page
Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers');
Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
Route::patch('/suppliers/{supplier}/toggle-status', [SupplierController::class, 'toggleStatus'])->name('suppliers.toggleStatus');


// Customers Page
Route::get('/customers', [CustomerController::class, 'index'])->name('customers');
Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');


// Expenses Page
Route::middleware('auth')->group(function () {
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses');
    Route::get('/expenses/list', [ExpenseController::class, 'list'])->name('expenses.list');
    Route::get('/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->whereNumber('expense')->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->whereNumber('expense')->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->whereNumber('expense')->name('expenses.destroy');

    // Expense Types
    Route::get('/expenses/categories', [ExpenseCategoryController::class, 'index'])->name('expenses.categories');
    Route::post('/expenses/categories', [ExpenseCategoryController::class, 'store'])->name('expenses.categories.store');
    Route::get('/expenses/categories/{category}/edit', [ExpenseCategoryController::class, 'edit'])->name('expenses.categories.edit');
    Route::put('/expenses/categories/{category}', [ExpenseCategoryController::class, 'update'])->name('expenses.categories.update');
    Route::patch('/expenses/categories/{category}/toggle-status', [ExpenseCategoryController::class, 'toggleStatus'])->name('expenses.categories.toggleStatus');
    Route::delete('/expenses/categories/{category}', [ExpenseCategoryController::class, 'destroy'])->name('expenses.categories.destroy');
});


// Settings Page
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
});