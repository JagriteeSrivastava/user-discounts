<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DemoController;

Route::get('/', [DemoController::class, 'index'])->name('demo.index');
Route::post('/assign', [DemoController::class, 'assign'])->name('demo.assign');
Route::post('/revoke', [DemoController::class, 'revoke'])->name('demo.revoke');
Route::get('/audits', [DemoController::class, 'audits'])->name('demo.audits');

// Discount CRUD
use App\Http\Controllers\DiscountController;

Route::resource('discounts', DiscountController::class);
