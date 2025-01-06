<?php

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerSearchController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BranchOfficerController;
use App\Http\Controllers\Admin\ReceiptStockController;
use App\Http\Controllers\Admin\ReceiptDistributionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
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

    Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'downloadPdf'])->name('admin.reports.download');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Branch Officers Routes
    Route::resource('branch-officers', BranchOfficerController::class);
    Route::get('branch-officers/by-branch/{branch}', [BranchOfficerController::class, 'getByBranch'])
        ->name('branch-officers.by-branch');

    // Receipt Stock Routes
    Route::get('receipt-stocks', [ReceiptStockController::class, 'index'])->name('receipt-stocks.index');
    Route::post('receipt-stocks/transfer', [ReceiptStockController::class, 'transfer'])->name('receipt-stocks.transfer');
    Route::get('receipt-stocks/history/{branch}', [ReceiptStockController::class, 'history'])->name('receipt-stocks.history');

    // Receipt Distribution Routes
    Route::get('receipt-distributions/create', [ReceiptDistributionController::class, 'create'])->name('receipt-distributions.create');
    Route::post('receipt-distributions', [ReceiptDistributionController::class, 'store'])->name('receipt-distributions.store');
    Route::get('receipt-stocks/{branch}/distributions', [ReceiptStockController::class, 'distributions'])
    ->name('receipt-stocks.distributions');
});


require __DIR__.'/auth.php';
