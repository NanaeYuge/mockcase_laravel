<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\StripeCheckoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

/* 公開（認証不要） */
Route::get('/', [ItemController::class, 'index'])->name('home');
Route::get('/items', [ItemController::class, 'index'])->name('items.index');
Route::get('/items/{id}', [ItemController::class, 'show'])->whereNumber('id')->name('items.show');

/* 認証関連 */
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);


Route::post('/stripe/webhook', [StripeCheckoutController::class, 'webhook'])->name('stripe.webhook');

/* 認証必須 */
Route::middleware(['auth'])->group(function () {

    /* マイページ */
    Route::get('/mypage', [ProfileController::class, 'show'])->name('mypage');
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    /* いいね */
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'store'])->whereNumber('item')->name('items.favorite');
    Route::delete('/items/{item}/favorite', [FavoriteController::class, 'destroy'])->whereNumber('item')->name('items.unfavorite');

    /* コメント */
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->whereNumber('item')->name('comments.store');

    /* 購入フロー */
    Route::get('/purchase/{id}', [OrderController::class, 'create'])->whereNumber('id')->name('purchase.create');
    Route::post('/purchase/{id}', [OrderController::class, 'store'])->whereNumber('id')->name('purchase.store');

    /* 住所編集 */
    Route::get('/purchase/address/{id}', [OrderController::class, 'editAddress'])->whereNumber('id')->name('purchase.address.edit');
    Route::post('/purchase/address/{id}', [OrderController::class, 'updateAddress'])->whereNumber('id')->name('purchase.address.update');

    /* 支払い方法の一時保存 */
    Route::post('/purchase/{id}/payment', [OrderController::class, 'savePaymentMethod'])->whereNumber('id')->name('purchase.payment.save');

    /* Stripe PI 生成 */
    Route::post('/stripe/pi/{order}', [OrderController::class, 'createPaymentIntent'])->whereNumber('order')->name('stripe.pi.create');

    /* 出品・編集 */
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->whereNumber('item')->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->whereNumber('item')->name('items.update');

    Route::post('/purchase/{item}/checkout', [StripeCheckoutController::class, 'checkout'])->name('purchase.checkout');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
