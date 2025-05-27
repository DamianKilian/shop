<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'payment',
    'as'         => 'payment-',
], function () {
    Route::post('pay', [PaymentController::class, 'pay'])->name('pay');
});
