<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SmartFinanceController;
use App\Http\Controllers\SalesForumController;

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminTransactionController;

use App\Http\Controllers\SavingPlanController; //

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes (dari Breeze)
require __DIR__.'/auth.php';

// Protected Routes (harus login)
Route::middleware(['auth'])->group(function () { // ✅ HAPUS 'verified' DARI SINI
    
    // Dashboard utama berdasarkan role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ==================== TRANSACTION ROUTES ====================
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('/history', [TransactionController::class, 'history'])->name('history');
        
        // Top Up
        Route::get('/topup', [TransactionController::class, 'topupIndex'])->name('topup');
        Route::post('/topup/review', [TransactionController::class, 'topupReview'])->name('topup.review');
        Route::post('/topup', [TransactionController::class, 'topupStore'])->name('topup.store');
        
        // Transfer
        Route::get('/transfer', [TransactionController::class, 'transferIndex'])->name('transfer');
        Route::post('/transfer', [TransactionController::class, 'transferStore'])->name('transfer.store');
        
        // Withdraw
        Route::get('/withdraw', [TransactionController::class, 'withdrawIndex'])->name('withdraw');
        Route::post('/withdraw', [TransactionController::class, 'withdrawStore'])->name('withdraw.store');
        
        // Payment
        Route::get('/payment', [TransactionController::class, 'paymentIndex'])->name('payment');
        Route::post('/payment', [TransactionController::class, 'paymentStore'])->name('payment.store');
    });
    
    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard-new');
        })->name('dashboard');
        
        // User Management Routes
        Route::resource('users', AdminUserController::class);
        
        // Transaction Management Routes
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/', [AdminTransactionController::class, 'index'])->name('index');
            Route::get('/{user}/detail', [AdminTransactionController::class, 'userDetail'])->name('user-detail');
            // Top-up reporting
            Route::get('/topups', [AdminTransactionController::class, 'topUpReport'])->name('topups');
            // Transfer reporting
            Route::get('/transfers', [AdminTransactionController::class, 'transferReport'])->name('transfers');
            // Weekly transaction history (last 7 days)
            Route::get('/weekly', [AdminTransactionController::class, 'weeklyHistory'])->name('weekly');
        });
        
        Route::get('/umkm', function () {
            return view('admin.umkm');
        })->name('umkm');

        // Admin Smart Finance
        Route::prefix('smart-finance')->name('smartfinance.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminSmartFinanceController::class, 'index'])->name('index');
            // Templates CRUD
            Route::resource('templates', \App\Http\Controllers\Admin\AdminSmartFinanceTemplateController::class)->names('templates');
            Route::get('/{user}', [\App\Http\Controllers\Admin\AdminSmartFinanceController::class, 'show'])->name('show');
            Route::post('/{user}/recommend', [\App\Http\Controllers\Admin\AdminSmartFinanceController::class, 'storeRecommendation'])->name('recommend');
        });

    // ==================== SAVINGS ROUTES (BARU) ====================
    Route::prefix('savings')->name('savings.')->group(function () {
        // Main CRUD routes
        Route::get('/', [SavingPlanController::class, 'index'])->name('index');
        Route::get('/create', [SavingPlanController::class, 'create'])->name('create');
        Route::post('/', [SavingPlanController::class, 'store'])->name('store');
        Route::get('/{id}', [SavingPlanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [SavingPlanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SavingPlanController::class, 'update'])->name('update');
        Route::delete('/{id}', [SavingPlanController::class, 'destroy'])->name('destroy');
        
        // Additional functionality
        Route::post('/{id}/add-funds', [SavingPlanController::class, 'addFunds'])->name('add-funds');
        Route::get('/{id}/transactions', [SavingPlanController::class, 'transactions'])->name('transactions');
        Route::get('/statistics', [SavingPlanController::class, 'statistics'])->name('statistics');
        
        // Quick actions (optional)
        Route::post('/{id}/complete', [SavingPlanController::class, 'complete'])->name('complete');
        Route::post('/{id}/reactivate', [SavingPlanController::class, 'reactivate'])->name('reactivate');
    });
    
    // ==================== SMART FINANCE ROUTES ====================
    Route::get('/smart-finance', [SmartFinanceController::class, 'index'])
        ->name('smartfinance.index');
    
    // ==================== SALES FORUM ROUTES ====================
    Route::prefix('sales-forum')->name('sales_forum.')->group(function () {
        Route::get('/', [SalesForumController::class, 'index'])->name('index');
        Route::get('/create', [SalesForumController::class, 'create'])->name('create');
        Route::post('/', [SalesForumController::class, 'store'])->name('store');
        Route::get('/{salesForum}', [SalesForumController::class, 'show'])->name('show');
        Route::get('/{salesForum}/edit', [SalesForumController::class, 'edit'])->name('edit');
        Route::patch('/{salesForum}', [SalesForumController::class, 'update'])->name('update');
        Route::delete('/{salesForum}', [SalesForumController::class, 'destroy'])->name('destroy');
        Route::post('/{salesForum}/mark-sold', [SalesForumController::class, 'markAsSold'])->name('mark_sold');
    });
    
    // ==================== PROFILE ROUTES ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // UMKM Upgrade Routes
    Route::get('/umkm/upgrade', [ProfileController::class, 'showUpgradeForm'])->name('umkm.upgrade');
    Route::post('/umkm/upgrade', [ProfileController::class, 'upgradeToUmkm'])->name('umkm.upgrade.post');
    
    // Profile Photo Routes
    Route::post('/profile/photo', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');