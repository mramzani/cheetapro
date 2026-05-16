<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ClientController as AdminClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SellerController as AdminSellerController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Seller\AuthController as SellerAuthController;
use App\Http\Controllers\Seller\ClientController as SellerClientController;
use App\Http\Controllers\Seller\DashboardController as SellerDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('seller.login');
});

Route::get('login', function () {
    return redirect()->route('seller.login');
})->name('login');

Route::middleware('guest')->prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [AdminAuthController::class, 'create'])->name('login');
    Route::post('login', [AdminAuthController::class, 'store'])->name('login.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('logout', [AdminAuthController::class, 'destroy'])->name('logout');
    Route::get('dashboard', AdminDashboardController::class)->name('dashboard');
    Route::resource('sellers', AdminSellerController::class)->only(['index', 'store', 'edit', 'update']);
    Route::get('sellers/{seller}/wallet/charge', [AdminWalletController::class, 'create'])->name('wallets.charge');
    Route::post('sellers/{seller}/wallet/charge', [AdminWalletController::class, 'store'])->name('wallets.charge.store');
    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [AdminSettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-xui', [AdminSettingController::class, 'test'])->name('settings.test');
    Route::get('clients', [AdminClientController::class, 'index'])->name('clients.index');
    Route::post('clients/sync', [AdminClientController::class, 'sync'])->name('clients.sync');
});

Route::middleware('guest:seller')->prefix('seller')->name('seller.')->group(function () {
    Route::get('login', [SellerAuthController::class, 'create'])->name('login');
    Route::post('login', [SellerAuthController::class, 'store'])->name('login.store');
});

Route::middleware(['auth:seller', 'seller.active'])->prefix('seller')->name('seller.')->group(function () {
    Route::post('logout', [SellerAuthController::class, 'destroy'])->name('logout');
    Route::get('dashboard', SellerDashboardController::class)->name('dashboard');
    Route::get('clients', [SellerClientController::class, 'index'])->name('clients.index');
    Route::post('clients/sync', [SellerClientController::class, 'sync'])->name('clients.sync');
    Route::get('clients/create', [SellerClientController::class, 'create'])->name('clients.create');
    Route::post('clients', [SellerClientController::class, 'store'])->name('clients.store');
    Route::get('clients/{client}', [SellerClientController::class, 'show'])->name('clients.show');
});
