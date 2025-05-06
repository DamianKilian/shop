<?php

use App\Http\Controllers\Przelewy24Controller;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix'     => 'przelewy24',
    'as'         => 'przelewy24-',
], function () {
    Route::post('transaction-register', [Przelewy24Controller::class, 'transactionRegister'])->name('transaction-register');
});
