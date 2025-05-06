<?php

use App\Http\Controllers\Przelewy24Controller;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'przelewy24',
    'as'         => 'przelewy24-',
], function () {
    Route::post('transaction-status', [Przelewy24Controller::class, 'transactionStatus'])->name('transaction-status');
});
