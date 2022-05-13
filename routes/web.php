<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PaymentController::class, 'index'])->name('home');

Route::group(['as' => 'billing.', 'prefix' => 'billing'], static function () {

    Route::post('process', [PaymentController::class, 'processPayment'])->name('process');
    Route::group(['as' => 'gateway.', 'prefix' => '{gateway}'], static function () {
        Route::any('return', [PaymentController::class, 'gatewayReturn'])->name('complete');

        Route::any('cancel', [PaymentController::class, 'gatewayCancel'])->name('cancel');
        Route::get('pending', [PaymentController::class, 'pending'])->name('pending');
        Route::any('success', [PaymentController::class, 'success'])->name('success');

        Route::get('status', [PaymentController::class, 'status'])->name('status');
    });
});
