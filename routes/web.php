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

// Payment gateway webhook (public)
Route::post('/webhook/payment', [\App\Http\Controllers\Webhook\PaymentWebhookController::class, 'handle'])->name('webhook.payment');

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

        // UMKM Applications (admin review)
        Route::prefix('umkm-applications')->name('umkm_applications.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminUmkmApplicationController::class, 'index'])->name('index');
            Route::get('/{application}', [\App\Http\Controllers\Admin\AdminUmkmApplicationController::class, 'show'])->name('show');
            Route::post('/{application}/approve', [\App\Http\Controllers\Admin\AdminUmkmApplicationController::class, 'approve'])->name('approve');
            Route::post('/{application}/reject', [\App\Http\Controllers\Admin\AdminUmkmApplicationController::class, 'reject'])->name('reject');
        });

        // Withdraw management
        Route::prefix('withdraws')->name('withdraws.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'index'])->name('index');
            Route::get('/{id}', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'reject'])->name('reject');
            Route::post('/{id}/complete', [\App\Http\Controllers\Admin\AdminWithdrawController::class, 'complete'])->name('complete');
        });

    // close admin group
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
        Route::post('/{salesForum}/purchase', [SalesForumController::class, 'purchase'])->name('purchase');
        Route::get('/{salesForum}/edit', [SalesForumController::class, 'edit'])->name('edit');
        Route::patch('/{salesForum}', [SalesForumController::class, 'update'])->name('update');
        Route::delete('/{salesForum}', [SalesForumController::class, 'destroy'])->name('destroy');
        Route::post('/{salesForum}/mark-sold', [SalesForumController::class, 'markAsSold'])->name('mark_sold');
    });
    
    // ==================== UMKM MERCHANT ROUTES ====================
    // Seller (merchant) routes for sellers
    Route::prefix('seller')->name('seller.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Seller\SellerWalletController::class, 'index'])->name('dashboard');
        // Dedicated seller UI (separate dashboard URL for testing)
        Route::get('/dashboard', [\App\Http\Controllers\Seller\DashboardController::class, 'index']);
        Route::get('/transactions', [\App\Http\Controllers\Seller\SellerWalletController::class, 'transactions'])->name('transactions');
        Route::post('/withdraw', [\App\Http\Controllers\Seller\SellerWalletController::class, 'withdraw'])->name('withdraw');
    });

    // Legacy UMKM merchant routes (kept for backward compatibility)
    Route::middleware(['auth','role:umkm'])->prefix('umkm')->name('umkm.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Umkm\MerchantController::class, 'index'])->name('dashboard');
        Route::resource('products', \App\Http\Controllers\Umkm\UmkmProductController::class);
        Route::get('/orders', [\App\Http\Controllers\Umkm\UmkmOrderController::class, 'index'])->name('orders.index');
        // Wallet routes for UMKM merchant
        Route::get('/wallet', [\App\Http\Controllers\Umkm\WalletController::class, 'show'])->name('wallet.show');
        Route::get('/wallet/history', [\App\Http\Controllers\Umkm\WalletController::class, 'history'])->name('wallet.history');
        Route::post('/wallet/transfer', [\App\Http\Controllers\Umkm\WalletController::class, 'transferToUser'])->name('wallet.transfer');
        Route::post('/wallet/internal-transfer', [\App\Http\Controllers\Umkm\WalletController::class, 'internalTransfer'])->name('wallet.internal_transfer');
        Route::post('/wallet/withdraw', [\App\Http\Controllers\Umkm\WalletController::class, 'withdraw'])->name('wallet.withdraw');
        // Merchant purchase order management
        Route::get('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'merchantIndex'])->name('purchase_orders.merchant_index');
        Route::post('/purchase-orders/{order}/confirm', [\App\Http\Controllers\PurchaseOrderController::class, 'merchantConfirm'])->name('purchase_orders.merchant_confirm');
    });
    
    // ==================== PROFILE ROUTES ====================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // UMKM Upgrade Routes
    Route::get('/umkm/upgrade', [ProfileController::class, 'showUpgradeForm'])->name('umkm.upgrade');
    Route::post('/umkm/upgrade', [ProfileController::class, 'upgradeToUmkm'])->name('umkm.upgrade.post');

    // Purchase Orders (buyer flow)
    Route::post('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('purchase_orders.store');
    Route::get('/purchase-orders/{reference}', [\App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('purchase_orders.show');
    Route::post('/purchase-orders/{order}/upload-proof', [\App\Http\Controllers\PurchaseOrderController::class, 'uploadProof'])->name('purchase_orders.upload_proof');
    
    // Profile Photo Routes
    Route::post('/profile/photo', [ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo', [ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
});

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');