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
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Auth required routes (checkout/mypage)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process')
        ->middleware('throttle:5,1'); // 決済は1分に5回まで
    Route::get('/checkout/thanks', [CheckoutController::class, 'thanks'])->name('checkout.thanks');
    Route::get('/mypage', [MyPageController::class, 'index'])->name('mypage');
    Route::post('/mypage/skip', [MyPageController::class, 'skipSubscription'])->name('mypage.skip');
    Route::post('/mypage/cancel', [MyPageController::class, 'cancelSubscription'])->name('mypage.cancel');
    Route::post('/mypage/change-plan', [MyPageController::class, 'changePlan'])->name('mypage.change-plan');
    Route::post('/mypage/billing-date', [MyPageController::class, 'changeBillingDate'])->name('mypage.billing-date');
    Route::post('/mypage/pause', [MyPageController::class, 'pauseSubscription'])->name('mypage.pause');
    Route::post('/mypage/resume', [MyPageController::class, 'resumeSubscription'])->name('mypage.resume');
    Route::post('/mypage/refund/{chargeId}', [MyPageController::class, 'refund'])->name('mypage.refund')
        ->middleware('throttle:3,1');
});

// API routes
Route::prefix('api')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/diagnosis/questions', [DiagnosisApiController::class, 'questions']);
    Route::post('/diagnosis/submit', [DiagnosisApiController::class, 'submit'])
        ->middleware('throttle:10,1'); // 診断送信は1分に10回まで
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
