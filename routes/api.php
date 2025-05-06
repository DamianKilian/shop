<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/apiPrzelewy24.php';

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/get-suggestions', [HomeController::class, 'getSuggestions'])->name('get-suggestions');
Route::post('/get-products-view', [HomeController::class, 'getProductsView'])->name('get-products-view');
Route::post('/get-products-view-all-categories', [HomeController::class, 'getProductsViewAllCategories'])->name('get-products-view-all-categories');
Route::post('/get-product-nums', [HomeController::class, 'getProductNums'])->name('get-product-nums');
Route::prefix('basket')->group(function () {
    Route::post('/get-products-in-basket-data', [BasketController::class, 'getProductsInBasketData'])->name('get-products-in-basket-data');
    Route::post('/get-basket-summary', [BasketController::class, 'getBasketSummary'])->name('get-basket-summary');
});
Route::post('/log-js', [LogController::class, 'logJs'])->name('log-js');
Route::prefix('account')->group(function () {
    Route::post('/get-area-codes', [App\Http\Controllers\Account\AddressController::class, 'getAreaCodes'])->name('get-area-codes')->withoutMiddleware(['auth']);
});
