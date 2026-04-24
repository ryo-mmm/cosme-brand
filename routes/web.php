<?php

use App\Http\Controllers\TopController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\DiagnosisApiController;
use Illuminate\Support\Facades\Route;

// Top
Route::get('/', [TopController::class, 'index'])->name('top');

// Skin Diagnosis (SPA shell + results)
Route::get('/diagnosis', [DiagnosisController::class, 'index'])->name('diagnosis');
Route::get('/diagnosis/result/{id}', [DiagnosisController::class, 'result'])->name('diagnosis.result');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Auth required routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('mypage'))->name('dashboard');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');
    Route::post('/mypage/skip', [MyPageController::class, 'skipSubscription'])->name('mypage.skip');
    Route::post('/mypage/cancel', [MyPageController::class, 'cancelSubscription'])->name('mypage.cancel');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API routes
Route::prefix('api')->group(function () {
    Route::get('/diagnosis/questions', [DiagnosisApiController::class, 'questions']);
    Route::post('/diagnosis/submit', [DiagnosisApiController::class, 'submit']);
});

require __DIR__.'/auth.php';
