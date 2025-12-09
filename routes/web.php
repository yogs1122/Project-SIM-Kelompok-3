<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SmartFinanceController;
use App\Http\Controllers\SalesForumController;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (dari Breeze)
require __DIR__.'/auth.php';

// Protected Routes (harus login)
Route::middleware(['auth'])->group(function () {
    
    // Dashboard utama berdasarkan role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transaction Routes
    Route::prefix('transactions')->group(function () {
        Route::get('/history', [TransactionController::class, 'history'])->name('transactions.history');
        
        // Top Up
        Route::get('/topup', [TransactionController::class, 'topupIndex'])->name('transactions.topup');
        Route::post('/topup/review', [TransactionController::class, 'topupReview'])->name('transactions.topup.review');
        Route::post('/topup', [TransactionController::class, 'topupStore'])->name('transactions.topup.store');
        
        // Transfer
        Route::get('/transfer', [TransactionController::class, 'transferIndex'])->name('transactions.transfer');
        Route::post('/transfer', [TransactionController::class, 'transferStore'])->name('transactions.transfer.store');
        
        // Withdraw
        Route::get('/withdraw', [TransactionController::class, 'withdrawIndex'])->name('transactions.withdraw');
        Route::post('/withdraw', [TransactionController::class, 'withdrawStore'])->name('transactions.withdraw.store');
        
        // Payment
        Route::get('/payment', [TransactionController::class, 'paymentIndex'])->name('transactions.payment');
        Route::post('/payment', [TransactionController::class, 'paymentStore'])->name('transactions.payment.store');
    });
    
    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
    });
    
    // Profile Routes (dari Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Profile Photo Routes
    Route::post('/profile/photo', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
});

 Route::middleware(['auth'])->group(function () {
    Route::get('/smart-finance', [SmartFinanceController::class, 'index'])
        ->name('smartfinance.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/sales-forum', [SalesForumController::class, 'index'])->name('sales_forum.index');
    Route::get('/sales-forum/create', [SalesForumController::class, 'create'])->name('sales_forum.create');
    Route::post('/sales-forum', [SalesForumController::class, 'store'])->name('sales_forum.store');
    Route::get('/sales-forum/{salesForum}', [SalesForumController::class, 'show'])->name('sales_forum.show');
    Route::get('/sales-forum/{salesForum}/edit', [SalesForumController::class, 'edit'])->name('sales_forum.edit');
    Route::patch('/sales-forum/{salesForum}', [SalesForumController::class, 'update'])->name('sales_forum.update');
    Route::delete('/sales-forum/{salesForum}', [SalesForumController::class, 'destroy'])->name('sales_forum.destroy');
    Route::post('/sales-forum/{salesForum}/mark-sold', [SalesForumController::class, 'markAsSold'])->name('sales_forum.mark_sold');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');


