<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:30,1')->name('login.store');
});
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/', [MemberController::class, 'index'])->name('home');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/applications/create', [TransactionController::class, 'createApplication'])->name('transactions.applications.create');
    Route::post('transactions/applications', [TransactionController::class, 'storeApplication'])->name('transactions.applications.store');
    Route::get('transactions/renewals/create', [TransactionController::class, 'createRenewal'])->name('transactions.renewals.create');
    Route::post('transactions/renewals', [TransactionController::class, 'storeRenewal'])->name('transactions.renewals.store');
    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('transactions/{transaction}/payments', [PaymentController::class, 'store'])->name('transactions.payments.store');
    Route::post('transactions/{transaction}/approve', [PaymentController::class, 'approve'])->name('transactions.approve');
    Route::get('payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
    Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');

    Route::resource('members', MemberController::class)->except('destroy');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('levels', LevelController::class)->except('show');
    Route::patch('members/{member}/deactivate', [MemberController::class, 'deactivate'])->name('members.deactivate');
    Route::patch('members/{member}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');

});
