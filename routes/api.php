<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post('/get-suggestions', [HomeController::class, 'getSuggestions'])->name('get-suggestions');
Route::post('/get-products-view', [HomeController::class, 'getProductsView'])->name('get-products-view');
Route::post('/get-products-view-all-categories', [HomeController::class, 'getProductsViewAllCategories'])->name('get-products-view-all-categories');
Route::post('/get-product-nums', [HomeController::class, 'getProductNums'])->name('get-product-nums');
Route::prefix('basket')->group(function () {
    Route::post('/get-products-in-basket-data', [BasketController::class, 'getProductsInBasketData'])->name('get-products-in-basket-data');
});