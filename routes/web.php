<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// 1. Public / Redirect Routes
Route::get('/', fn() => redirect()->route('dashboard'));

// 2. Authenticated Routes
Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // Profile Management
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    // Product Management (CRUD) - Menggunakan Resource agar lebih singkat
    Route::resource('products', ProductController::class);

    // Kasir & Transaction Management
    Route::prefix('transactions')->group(function () {
        Route::get('/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/checkout', [TransactionController::class, 'checkout'])->name('transactions.checkout');
        // Tambahkan di dalam Route::prefix('transactions')
        Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('/{id}', [TransactionController::class, 'show'])->name('transactions.show');
    });

    // Cart Management
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add', [TransactionController::class, 'addToCart'])->name('add');
        Route::post('/remove', [TransactionController::class, 'removeFromCart'])->name('remove');
        Route::post('/update', [TransactionController::class, 'updateCart'])->name('update');
    });

});

require __DIR__.'/auth.php';