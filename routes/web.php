<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerSearchController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::get('/', [AuthenticatedSessionController::class, 'create'])
->name('login');

Route::get('/storage-link', function () {
    Artisan::call('storage:link');

    return response()->json(['message' => 'Storage link created successfully.']);
})->name('storage.link');

// Route for running migrations
Route::get('/migrate', function () {
    Artisan::call('migrate');

    return response()->json(['message' => 'Migrations run successfully.']);
})->name('migrate');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('/admin/branches', BranchController::class)->names('admin.branches');
    Route::resource('admin/users', UserController::class)->names('admin.users');
    Route::resource('admin/customers', CustomerController::class)->names('admin.customers');

    Route::get('/customer-search', [CustomerSearchController::class, 'search'])->name('customer.search');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
