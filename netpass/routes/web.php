<?php

use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MemberController::class, 'index'])->name('home');

Route::resource('members', MemberController::class)->except('destroy');
Route::patch('members/{member}/deactivate', [MemberController::class, 'deactivate'])->name('members.deactivate');
Route::patch('members/{member}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');
