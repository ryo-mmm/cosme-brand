<?php

use App\Http\Controllers\TopController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StripeWebhookController;
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
    Route::get('/checkout/thanks', [CheckoutController::class, 'thanks'])->name('checkout.thanks');
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

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::get('/products/{product}/edit', [AdminController::class, 'productEdit'])->name('products.edit');
    Route::patch('/products/{product}', [AdminController::class, 'productUpdate'])->name('products.update');
    Route::patch('/products/{product}/toggle', [AdminController::class, 'productToggle'])->name('products.toggle');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
});

// Stripe Webhook (CSRF excluded in bootstrap/app.php)
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);

// Legal pages
Route::get('/legal/tokushoho', fn() => view('legal.tokushoho'))->name('legal.tokushoho');
Route::get('/legal/privacy', fn() => view('legal.privacy'))->name('legal.privacy');
Route::get('/legal/terms', fn() => view('legal.terms'))->name('legal.terms');

require __DIR__.'/auth.php';
