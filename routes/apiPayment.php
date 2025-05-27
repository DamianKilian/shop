<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'payment',
    'as'         => 'payment-',
], function () {
    Route::post('transaction-status', [PaymentController::class, 'transactionStatus'])->name('transaction-status');
});
