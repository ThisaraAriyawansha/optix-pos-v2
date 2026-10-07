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
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\WorkActivityController;
use App\Http\Controllers\LabourController;
use App\Http\Controllers\WorkerController;
use App\Http\Controllers\WorkEntryController;
use App\Http\Controllers\SalaryPaymentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceAdminController;
use App\Http\Controllers\AttendanceDeviceController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;



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


// Products Page — everyone sees products and selling prices; costs and pricing are admin-only
Route::middleware('auth')->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products');

    Route::middleware('can:manage-pricing')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->whereNumber('product')->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->whereNumber('product')->name('products.update');
        Route::patch('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->whereNumber('product')->name('products.toggleStatus');

        // Raw Materials
        Route::get('/products/materials', [RawMaterialController::class, 'index'])->name('products.materials');
        Route::post('/products/materials', [RawMaterialController::class, 'store'])->name('products.materials.store');
        Route::get('/products/materials/{material}/edit', [RawMaterialController::class, 'edit'])->name('products.materials.edit');
        Route::put('/products/materials/{material}', [RawMaterialController::class, 'update'])->name('products.materials.update');
        Route::patch('/products/materials/{material}/toggle-status', [RawMaterialController::class, 'toggleStatus'])->name('products.materials.toggleStatus');

        // Work Activities & Rates
        Route::get('/products/activities', [WorkActivityController::class, 'index'])->name('products.activities');
        Route::post('/products/activities', [WorkActivityController::class, 'store'])->name('products.activities.store');
        Route::get('/products/activities/{activity}/edit', [WorkActivityController::class, 'edit'])->name('products.activities.edit');
        Route::put('/products/activities/{activity}', [WorkActivityController::class, 'update'])->name('products.activities.update');
        Route::patch('/products/activities/{activity}/toggle-status', [WorkActivityController::class, 'toggleStatus'])->name('products.activities.toggleStatus');
    });
});


// Labour, Attendance & Salary
Route::middleware('auth')->group(function () {
    Route::get('/labour', [LabourController::class, 'index'])->name('labour');
    Route::get('/labour/production', [LabourController::class, 'production'])->name('labour.production');

    // Workers
    Route::get('/labour/workers', [WorkerController::class, 'index'])->name('labour.workers');
    Route::get('/labour/workers/create', [WorkerController::class, 'create'])->name('labour.workers.create');
    Route::post('/labour/workers', [WorkerController::class, 'store'])->name('labour.workers.store');
    Route::get('/labour/workers/{worker}/edit', [WorkerController::class, 'edit'])->name('labour.workers.edit');
    Route::put('/labour/workers/{worker}', [WorkerController::class, 'update'])->name('labour.workers.update');
    Route::patch('/labour/workers/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('labour.workers.toggleStatus');

    // Daily Work Entry
    Route::get('/labour/work', [WorkEntryController::class, 'index'])->name('labour.work');
    Route::get('/labour/work/create', [WorkEntryController::class, 'create'])->name('labour.work.create');
    Route::post('/labour/work', [WorkEntryController::class, 'store'])->name('labour.work.store');
    Route::get('/labour/work/{entry}/edit', [WorkEntryController::class, 'edit'])->name('labour.work.edit');
    Route::put('/labour/work/{entry}', [WorkEntryController::class, 'update'])->name('labour.work.update');
    Route::delete('/labour/work/{entry}', [WorkEntryController::class, 'destroy'])->name('labour.work.destroy');

    // Salary Payments
    Route::get('/labour/salary', [SalaryPaymentController::class, 'index'])->name('labour.salary');
    Route::get('/labour/salary/create', [SalaryPaymentController::class, 'create'])->name('labour.salary.create');
    Route::post('/labour/salary', [SalaryPaymentController::class, 'store'])->name('labour.salary.store');
    Route::get('/labour/salary/{payment}', [SalaryPaymentController::class, 'show'])->whereNumber('payment')->name('labour.salary.show');
    Route::delete('/labour/salary/{payment}', [SalaryPaymentController::class, 'destroy'])->whereNumber('payment')->middleware('can:manage-pricing')->name('labour.salary.destroy');
});


// Attendance: check-in / check-out for labourers and staff (never admins)
Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkIn');
    Route::get('/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOutForm'])->whereNumber('attendance')->name('attendance.checkOut.form');
    Route::post('/attendance/{attendance}/check-out', [AttendanceController::class, 'checkOut'])->whereNumber('attendance')->name('attendance.checkOut');

    Route::middleware('can:manage-attendance')->group(function () {
        Route::get('/attendance/board', [AttendanceAdminController::class, 'board'])->name('attendance.board');
        Route::get('/attendance/report', [AttendanceAdminController::class, 'report'])->name('attendance.report');
        Route::get('/attendance/people', [AttendanceAdminController::class, 'people'])->name('attendance.people');
        Route::put('/attendance/people', [AttendanceAdminController::class, 'updatePeople'])->name('attendance.people.update');
        Route::get('/attendance/{attendance}/edit', [AttendanceAdminController::class, 'edit'])->whereNumber('attendance')->name('attendance.edit');
        Route::put('/attendance/{attendance}', [AttendanceAdminController::class, 'update'])->whereNumber('attendance')->name('attendance.update');
        Route::delete('/attendance/{attendance}', [AttendanceAdminController::class, 'destroy'])->whereNumber('attendance')->name('attendance.destroy');
    });
});

// Fingerprint devices (no login / CSRF — secured by device token or allowed serial numbers)
Route::withoutMiddleware([ValidateCsrfToken::class])->group(function () {
    Route::post('/attendance/device/punch', [AttendanceDeviceController::class, 'punch'])->name('attendance.device.punch');
    Route::get('/iclock/cdata', [AttendanceDeviceController::class, 'handshake']);
    Route::post('/iclock/cdata', [AttendanceDeviceController::class, 'receive']);
    Route::get('/iclock/getrequest', [AttendanceDeviceController::class, 'getRequest']);
    Route::post('/iclock/devicecmd', [AttendanceDeviceController::class, 'getRequest']);
});


// Help / How to use
Route::view('/help', 'frontend.help.index')->middleware('auth')->name('help');


// Settings Page
Route::middleware('auth')->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings');
    Route::get('/settings/profile', [SettingController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.profile.update');
});